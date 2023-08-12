<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Description :
     * redirect to to home page
     *
     * @return Response return to home page
     */
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * Description :
     * check if user is connected or not cennected
     *
     * @param SessionInterface $sessionInterface
     * @param UserRepository $userRepository
     * @return Response return user to JSON format
     */
    #[Route('/header', name: 'app_header')]
    public function header(SessionInterface $sessionInterface ,UserRepository $userRepository): Response
    {
        $user = null; // initialise user to null

        $userFromBdd = null; // initialise user from database to null
        
        $id = $sessionInterface->get('id'); // GET the user ID session

        // if user ID session is dinifed
        if($id) 
            $userFromBdd = $userRepository->findUserById($id); // find user by ID

        // if the user find by ID is difined
        if($userFromBdd) 
            $user = $userFromBdd;   // user = user find by ID

        return $this->json(['user'=>$user]);
    }

    /**
     * Description :
     * redirect to to error 404 page
     *
     * @return Response return to error 404 page
     */
    #[Route('/help', name: 'error_404')]
    public function help(): Response
    {
        return $this->render('404.html.twig');
    }
}
