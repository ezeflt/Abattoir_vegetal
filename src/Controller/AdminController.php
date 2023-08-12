<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{

    /**
     * Description :
     * check if the user is not the Admin
     * 
     * @param UserRepository is used to use functions that query the database (User document)
     * @param SessionInterface is used to GET the ID of the connected user 
     */
    public function checkIfUserIsAdmin(UserRepository $userRepository, SessionInterface $sessionInterface) 
    {
        $id = $sessionInterface->get('id');             // GET id session
        $connectedUser = $userRepository->find($id);    // find user by id
        
        // if id session is null or connected user has not 'ROLE_ADMIN'
        if ($id == null || !in_array('ROLE_ADMIN', $connectedUser->getRoles())){
            dd('you are not the admin');
            return $this->redirectToRoute('error_404');
        }
    }

    /**
     * Description :
     * redirect to admin page with all users from the database
     * 
     * @param UserRepository is used to use functions that query the database (User document)
     * @param sessionInterface is used to GET the ID of the connected user 
     * @return Response return to admin page with user data
     */
    #[Route('/', name: 'app_admin_index')]
    public function adminData(UserRepository $userRepository, SessionInterface $sessionInterface): Response 
    {
        $this->checkIfUserIsAdmin($userRepository, $sessionInterface);

        $allUsers = $userRepository->findAllFromBdd(); // GET all users from the database

        return $this->render('Admin/index.html.twig', [
            'users' => $allUsers,
        ]);
    }

    /**
     * Description :
     * Show the user selected with his ID
     * 
     * @param UserRepository is used to use functions that query the database
     * @param SessionInterface is used to GET the ID of the connected user 
     * @param id GET the user ID from URL
     * @return Response return to user profil with user data
     */
    #[Route('/{id}', name: 'app_admin_show')]
    public function showUser($id, UserRepository  $userRepository, SessionInterface $sessionInterface): Response
    {

        $this->checkIfUserIsAdmin($userRepository, $sessionInterface);

        $userSelected = $userRepository->findUserById($id);  // find user by id (param)

        // check if the user from databse is undifined
        if(!$userSelected)
            return $this->redirectToRoute('app_home'); // redirect to home page

        return $this->render('user_profil/profil.html.twig', [
            'user' => $userSelected,
        ]);
    }


    /**
     * Description :
     * Edit user data
     * 
     * @param UserRepository is used to use functions that query the database (User document)
     * @param request is used to GET form data
     * @param id GET the user ID from URL
     * @param Response return to user profil with his data
     */
    #[Route('/edit/{id}', name: 'app_admin_edit')]
    public function edit($id, Request $request, UserRepository $userRepository, SessionInterface $sessionInterface): Response
    {

        $this->checkIfUserIsAdmin($userRepository, $sessionInterface);

        $userFromBdd = $userRepository->findUserById($id);          // find user by id

        $form = $this->createForm(UserType::class, $userFromBdd);   // create a form for User document
        $form->handleRequest($request);                             // GET form data

        // check if form data is submitted and valid
        if($form->isSubmitted() && $form->isValid()) {

            $userRepository->save($userFromBdd, true);          // update user data and save it in database

            return $this->redirectToRoute('app_admin_index');   // redirect to admin page
        }

        return $this->render('user_profil/index.html.twig', [
            'UserForm' => $form->createView(),
            'user'     => $userFromBdd,
        ]);
    }

    /**
     * Description :
     * Delete user
     * 
     * @param UserRepository is used to use functions that query the database 
     * @param Request is used to GET form data
     * @param id GET the user ID from URL
     * @return Response return admin page
     */
    #[Route('/delete/{id}', name: 'app_admin_delete')]
    public function delete(Request $request, UserRepository $userRepository, SessionInterface $sessionInterface, $id): Response
    {

        $this->checkIfUserIsAdmin($userRepository, $sessionInterface);

        $userFromBdd = $userRepository->findUserById($id); // find user by id

        // Check if user is not undifined
        if($this->isCsrfTokenValid('delete'.$userFromBdd->getId(), $request->request->get('_token'))) {
            $userRepository->removeByDocument($userFromBdd, true); // delete user
        }
        return $this->redirectToRoute('app_admin_index');
    }


    /**
     * Description :
     * Show all groups from the database
     *
     * @param UserRepository is used to use functions that query the database  (User document)
     * @param GroupRepository is used to use functions that query the database (Group document)
     * @return Response return to Admin reservation page with all groups
     */
    #[Route('/showAllGroups', name: 'app_admin_grpindex')]
    public function showAllGroups(GroupRepository $groupeRepository, UserRepository $userRepository, SessionInterface $sessionInterface): Response 
    {
        $this->checkIfUserIsAdmin($userRepository, $sessionInterface);

        $groups = $groupeRepository->findAllGrpFromBdd(); // find all groups from the database

        return $this->render('Admin/reservation.html.twig', [
            'groups' => $groups,
        ]);
    }

    /**
     * Description :
     * Delete the group
     * 
     * @param UserRepository is used to use functions that query the database  (User document)
     * @param GroupRepository is used to use functions that query the database (Group document)
     * @param Request is used to GET the token
     * @param id GET the group ID from URL
     * @return Response return to admin group page
     */
    #[Route('/deleteGrp/{id}', name: 'app_grpadmin_delete')]
    public function deleteGroup(GroupRepository  $groupRepository, Request $request, $id , UserRepository $userRepository, SessionInterface $sessionInterface): Response
    {
        $this->checkIfUserIsAdmin($userRepository, $sessionInterface);

        $groupFromBdd = $groupRepository->find($id);     // the group from the database

        // Check the validity of the CSRF token
        if($this->isCsrfTokenValid('delete'.$groupFromBdd->getId(), $request->request->get('_token'))) {
            $groupRepository->removeByDocument($groupFromBdd, true); // delete the group from the database
        }

        return $this->redirectToRoute('app_admin_grpindex');
    }

}