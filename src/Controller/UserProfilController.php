<?php

namespace App\Controller;

use App\Document\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user_profil')]
class UserProfilController extends AbstractController
{
    #[Route('/edit', name: 'app_user_profil')]
    public function index(Request $request, UserRepository $userRepository, DocumentManager $documentManager, SessionInterface $sessionInterface): Response
    {

        $user  = new User();                                    // instancie user document
        
        $idSession = $sessionInterface->get('id');              // get id user from session

        if(!$idSession) 
            return $this->redirectToRoute('app_home'); // if idSession is undifined, then redirect to app_home
        
        $user = $userRepository->findUserById($idSession);      // get user by id session 
        
        if (!$user) 
            return $this->redirectToRoute('app_register');  // if user is undifined then redirect to app_register

        $form = $this->createForm(UserType::class, $user);      // create a form
        
        $form->handleRequest($request);                         // get the form datas


        // if data form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            
            //update the data to user in database
            $userRepository->save($user, true);
            // Redirect to success page
            return $this->redirectToRoute('app_user_profil_success');
        }

       return $this->render('user_profil/index.html.twig', [
            // send form for database
             'UserForm' => $form->createView(),
        ]); 
    }

    #[Route('/profil', name: 'app_user_profil_success')]
    public function success( UserRepository $userRepository, SessionInterface $sessionInterface): Response
    {
        // get id user from session
        $idSession = $sessionInterface->get('id');

        if(!$idSession){
            return $this->redirectToRoute('app_home');
        }

        // get document user from database
        $userFromBdd = $userRepository->findUserById($idSession);

        return $this->render('user_profil/profil.html.twig', [
            // send data user from database
            'user' => $userFromBdd,
        ]);
    }
}