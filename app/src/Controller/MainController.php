<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class MainController extends BaseController
{

    /**
     * @Route("/main", name="main")
     * @param UserRepository $userRepo
     * @return Response
     */
    public function index(UserRepository $userRepo): Response
    {
//         = new UserRepository();
//        $userRepo = $this->getDoctrine()->getRepository(User::class);
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'kokos' => $userRepo->findOneById(1)->getEmail()
        ]);
    }

}
