<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanterController extends AbstractController
{
    /**
     * @Route("/planter", name="planter")
     */
    public function index(): Response
    {
        return $this->render('planter/index.html.twig', [
            'controller_name' => 'PlanterController',
        ]);
    }
}
