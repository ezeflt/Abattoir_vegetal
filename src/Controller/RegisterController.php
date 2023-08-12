<?php

namespace App\Controller;

use App\Document\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register')]
class RegisterController extends AbstractController
{
    /**
     * Description :
     * registers a user in the database
     *
     * @param Request is used to filled in the form
     * @param UserRepository is used to use functions that query the database (User document)
     * @return Response return to register page with a form & alert message
     */
    #[Route('/add', name: 'app_register')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $user = new User(); // instantiates the class User

        $messageAlert = ''; // initialise the alert message
        
        $form = $this->createForm(RegisterType::class, $user);  // create form
        $form->handleRequest($request);  // filled in the form 

        // if from is submitted and valid then save the forms values in database
        if($form->isSubmitted() && $form->isValid() ){

            $email = $user->getEmail(); // GET user email

            // Email REGEX
            $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
            
            // Check if the email doesn't match the regular expression pattern
            if (!preg_match($regex, $email)|| strlen($email)>15) {
                $messageAlert = 'Invalid e-mail address. Please try again.';
            }

            // Check if the email doesn't exist in the database
            if(!$userRepository->checkEmailAlreadyExists($user->getEmail()) ){

                $passwordHash = password_hash($user->getPassword(), PASSWORD_BCRYPT); // hash the form password
                $user->setPassword($passwordHash); // UPDATE the password to the User class

                $userRepository->save($user);      // SAVE the user to database

                return $this->redirectToRoute('app_login'); // redirect to app_home route
            }else{
                // if email is already exsit 
                $messageAlert = 'this email already exists';  // messageAlert = this email already exists
            }
        }
        
        return $this->render('Register/index.html.twig',[
            'FormRegister' => $form->createView(),
            'alert' => $messageAlert
        ]);
    }
}