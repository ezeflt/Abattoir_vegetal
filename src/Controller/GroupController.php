<?php

namespace App\Controller;

use App\Document\Group;
use App\Document\Guest;
use App\Document\User;
use App\Form\ResaType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use MongoDB\BSON\ObjectId;

#[Route('/group')]
class GroupController extends AbstractController
{

    /**
     * Description :
     * redirect to the group page with form and checkbox data
     *
     * @param SessionInterface is used to GET the ID of the connected user 
     * @param Request is used to filled in the form
     * @return Response return redirect to group page with a form and checkbox data
     */
    #[Route('/select_group', name: 'app_select_group')]
    public function select_group(SessionInterface $sessionInterface,  Request $request): Response
    {
        $group = new Group();                               // instancie the Group class

        $idSession = $sessionInterface->get('id');          // GET the ID of the connected user

        $form = $this->createForm(ResaType::class, $group); // create form for document user
        $form->handleRequest($request);                     // filled in the form 

        // check if ID user is undifined
        if(empty($idSession))
            return $this->redirectToRoute('app_login');     //return to login page


        // creation of input checkbox data
        $dataCheckbox = [
            'Animaux',
            'Environnement',
            'Art et Culture',
            'Sport',
            'Voyage',
            'Musique',
            'Danse',
            'Sciences',
            'Bien-etre',
            'Food',
            'Activités sociales',
            'Jeux vidéos',
        ];

        return $this->render('Group/index.html.twig',[
            'dataFormCheckbox' => $dataCheckbox,
            'DateRegister' => $form->createView(),
        ]);
    }

    /**
     * Description :
     * redirect to usersList page with all users from the database
     *
     * @param DocumentManager is used to use functions that query the database
     * @return Response return usersList page with all users 
     */
    #[Route('/users-list/', name: 'app_get_users_list_without_filters')]
    public function showListOfUsers(DocumentManager $dm): Response
    {
        $users = []; // initialise the users array
        
        $iter = $dm->createQueryBuilder()   // create an SQL query
            ->find(User::class)             // find the user collection
            ->getQuery()                    // GET the query
            ->execute();                    // execute the query

        // while data $iter is valid
        while($iter->valid()) {
            $users[] = $iter->current();    // add a user to users array
            $iter->next();                  // add the next user
        }

        return $this->render('Group/usersList.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * Description :
     * redirect to userList page with the user filter 
     * this route is used when the user wishes to filter the list of users according to their interests
     * 
     * @param DocumentManager is used to use functions that query the database
     * @param filters is a list of interests in a string separated by a comma
     * @return Response return the userList page with user filter 
     */
    #[Route('/users-list/{filters}', name: 'app_get_users_list_from_filters')]
    public function showUsersList(string $filters, DocumentManager $dm): Response{

        $users = []; // initialise array user
        $filters = explode(',', $filters); // GET the $filters string and separates each interest by a comma in an array
        
        $iter = $dm->createQueryBuilder()               // create an SQL query
            ->find(User::class)                         // find the user collection
            ->field('centerOfInterest')->in($filters)   // filter centre of Interest which are the same as array $filters
            ->getQuery()                                // GET the query
            ->execute();                                // execute the query

        // while data $iter is valid
        while($iter->valid()) {
            $users[] = $iter->current(); // add a user to users array
            $iter->next();               // add the next user
        }

        return $this->render('Group/usersList.html.twig',[
            'users' => $users
        ]);
    }


    /**
     * Description :
     * update the pending invitations status (response to an invitation)
     * 
     * @param GroupRepository is used to use functions that query the database (Group document)
     * @param SessionInterface is used to GET the ID of the connected user      
     * @param DocumentManager is used to use functions that query the database
     * @param id is the group ID from the URL
     * @param bool is the response to the invitation
     */
    #[Route('/response/{id}/{bool}', name: 'response_Invitation')]
    public function responseInvitation($id, $bool,GroupRepository $groupRepository, SessionInterface $session, DocumentManager $dm): Response
    {
        $idGroup = $id; // GET the group ID

        $currentUserId = $session->get('id'); // GET the current user ID

        $group = $groupRepository->findOneBy(['id' => $idGroup]); // Find group by ID

        $guests = $group->getGuests(); // GET the guests from group

        // Iterate through the guests and update the invitation status for the matching user
        foreach ($guests as $guest) {
            
            $guestUserId = $guest->getGuest()->getId();  // GET each guest user id invitations

            // check if a guest user id = connected user id
            if ($guestUserId === $currentUserId) {
                $bool == 'true' && $guest->setInvitation('accept');   // if bool is true then updates the invitation to accept
                $bool == 'false' && $guest->setInvitation('refuse');  // if bool is false then updates the invitation to refuse
                break; // Stop the loop
            }
        }

        // Save the response to database
        $dm->flush();
        
        return $this->redirectToRoute('app_reservation');   // redirect to reservation page
    }

    /**
     * Description :
     * show all invitations 
     *
     * @param SessionInterface is used to GET the ID of the connected user  
     * @param GroupRepository is used to use functions that query the database (Group document)
     * @return Response return user notifications to JSON format
     */
    #[Route('/invitation', name: 'app_invitation')]
    public function invitation(SessionInterface $sessionInterface, GroupRepository $groupRepository): Response
    {

        $idSession = $sessionInterface->get('id');

        //appelle dans group ton id dans guest
        $notifInvitation = ($groupRepository->findBy([
            'status' => "waiting",
            'guests' => [
                '$elemMatch' => [
                    'invitation' => "waiting",
                    'guest.$id' => new ObjectId($idSession),
                ]
            ]
        ]));

        return $this->json(['notifications' => $notifInvitation]);
    }

    /**
     * Description :
     * create a group with several users to book a restaurant meeting
     * 
     * @param GroupRepository is used to use functions that query the database (Group document)
     * @param UserRepository is used to use functions that query the database (User document)
     * @param SessionInterface is used to GET the ID of the connected user 
     * @param idUsers is a list of user ID in a string separated by a comma
     * @param date is the date selected
     * @return Response redirect to reservation page
     */
    #[Route('/add/{idUsers}/{date}', name: 'app_add_group')]
    public function addGroup(string $idUsers, string $date, GroupRepository $groupRepo,  UserRepository $userRepo, SessionInterface $session): Response
    {
        $group = new Group();         // instancie class Group
        $user = new User();           // instancie class User
        $dateToday = new DateTime();  // initialise the current date 
        $idSession = $session->get('id');                           // GET ID from session
        $connectedUser = $userRepo->findUserById($idSession);       // GET user by ID from the database
        $dateSelected = DateTime::createFromFormat('Y-m-d', $date); // format the date as year month day 

        $authors = $connectedUser->username;  // GET username from connected User

        $dateSelected->modify('+2 hours');  // add 2 hours to the date selected for to have the current hour 
        $dateToday->modify('+2 hours');     // add 2 hours to the current date for to have current hour 

        // SET Group data
        $group->setStatus('waiting');              // SET the group status of group
        $group->setAuthors($authors);              // SET the authors of group
        $group->setReservationDate($dateSelected); // SET the reservation date of group
        $group->setCreateGroupDate($dateToday);    // SET the create group date of group

        $listIdUser = explode(',', $idUsers); // GET the $idUsers string and separates each interest by a comma in an array

        foreach($listIdUser as $id){
            $guest = new Guest();                       // instancie a new Guest class
            $user = $userRepo->findUserById($id);       // GET user by ID

            $guest->setGuest($user);                    // SET the Guest user
            $guest->setUsername($user->getUsername());  // SET the Guest username
            $guest->setInvitation('waiting');           // SET the status invitation of Guest

            $group->addGuest($guest);                   // ADD the Guest
        }
       
        $groupRepo->save($group);  // SAVE the group to database

        return $this->redirectToRoute('app_reservation');  // redirect to home page
    }
    
}