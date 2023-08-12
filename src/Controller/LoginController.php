<?php

namespace App\Controller;

use App\Document\User;
use App\Form\LoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * Description :
     * connect the user
     *
     * @param UserRepository is used to use functions that query the database (User document)
     * @param Request is used to filled in the form
     * @param SessionInterface is used to POST data to the session
     * @return Response return login page
     */
    #[Route('/login', name: 'app_login')]
    public function login(UserRepository $userRepository, Request $request, SessionInterface $sessionInterface): Response
    {
        $user = new User(); // instancie the User class

        $messageAlert = ''; // initialise alert message

        $form = $this->createForm(LoginType::class, $user); // create User form 
        $form->handleRequest($request);   // filled in the form 

        // form is submitted and valid
        if($form->isSubmitted() && $form->isValid()){

            $email = $user->getEmail();            // GET user email
            $password = $user->getPassword();      // GET User password

            // check if user email already exists
            if($userRepository->checkEmailAlreadyExists($email)){

                $userFromBdd = $userRepository->findUserByEmail($email);  // find user by Email

                // if the password hash and the form password is the same
                if(password_verify($password, $userFromBdd->password)){
                    
                    $sessionInterface->set('email', $userFromBdd->email); // add email to session
                    $sessionInterface->set('id', $userFromBdd->id); // add id to session
    
                    // if user interests is not undifined then redirect to your profil else profile to fill in
                    $result = !empty($userFromBdd->centerOfInterest) ? 'app_user_profil_success' : 'app_user_profil';
                    return $this->redirectToRoute($result);
                }else{
                    // alert message is Invalid password. Please try again.
                    $messageAlert = 'Invalid password. Please try again.';
                }
            }else{
                //if user email doesn't exist
                $messageAlert = "the email doesn't exist";
            }

        }

        return $this->render('Login/index.html.twig',[
            'Form' => $form->createView(),
            'alert' => $messageAlert
        ]);
    }

    /**
     * Description :
     * clear the session
     *
     * @param SessionInterface is used to clear the session
     * @return void redirect to homr page
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $sessionInterface)
    {
        $sessionInterface->clear();

        return $this->redirectToRoute('app_home');
    }  
}