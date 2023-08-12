<?php

namespace App\Controller;

use App\Document\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{

    /**
     * this route is used to fetch my reservations
     * and return the data in json format for use in javascript 
     *
     * @param SessionInterface SessionInterface is used to fetch the session datas
     * @param GroupRepository GroupRepository is used for functions that interact with the database (User Group)
     * @param UserRepository UserRepository is used for functions that interact with the database  (User document)
     * @return Response the response sent is an array of all my reservations in json format
     */
    #[Route('/allReservation', name: 'app_reservation')]
    public function allReservations(SessionInterface $sessionInterface, GroupRepository $groupRepository,UserRepository $userRepository, DocumentManager $dm): Response
    {
        $idSession = $sessionInterface->get('id');  // GET id session

        if(empty($idSession)) 
            // if $idSession is undefined then redirect to home
            return $this->redirectToRoute('app_home');                      
        

        $user = $userRepository->findUserById($idSession);   // find user by id
        $username = $user->username;  // GET username 

        // find all the groups for which I'm a guest
        $guest = $groupRepository->findGuestById($idSession, 'waiting');

        // find all the groups I've refused
        $declin = $groupRepository->findGuestById($idSession, 'refuse');

        // find all the groups I've accepted
        $accept = $groupRepository->findGuestById($idSession, 'accept');

        // finds all the groups to which each guest has responded
        $allUserAccept = $groupRepository->findReadyGroup($idSession);

        // find each group on which I am admin
        $authors = ($groupRepository->findBy(['authors' => "$username"]));

        $allReservation = [];       // initialise an array of all my reservations

        /**
         * loops over each received array 
         *  and push each data item to the array of all my reservations
         */
        foreach($accept as $guestAccept){
            array_push($allReservation, $guestAccept);
        }
        foreach($declin as $guestDeclin){
            array_push($allReservation, $guestDeclin);
        }
        foreach($guest as $guestToMe){
            array_push($allReservation, $guestToMe);
        }
        foreach($authors as $guestAuthors){
            array_push($allReservation, $guestAuthors);
        }

        // compare dates
        $sortByCreateGroupDate = function ($a, $b) {
            $dateA = $a->createdAt;
            $dateB = $b->createdAt;
        
            // Comparer dates and return the results
            return $dateA <=> $dateB;
        };
        
        // sort in ascending order
        usort($allReservation, $sortByCreateGroupDate);

        foreach ($allReservation as $reservation) {
            foreach ($reservation->guests as $guestItem) {
                $userReference = $guestItem->guest;
                $user = $dm->getReference(User::class, $userReference->id);
                $guestItem->guest->user = $user;
            }
        }

        // return the array of all my reservations and id session in json format for use it in javascript
        return $this->render('reservation/index.html.twig',[
            'allReservation' => $allReservation,
            'id' => $idSession,
            'username' => $username,
        ]);
    }
}
