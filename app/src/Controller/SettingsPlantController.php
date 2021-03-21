<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsPlantController extends AbstractController
{
    /**
     * @Route("/settings/plant", name="settings_plant")
     */
    public function index(): Response
    {
        return $this->render('settings_plant/index.html.twig', [
            'controller_name' => 'SettingsPlantController',
        ]);
    }
}
