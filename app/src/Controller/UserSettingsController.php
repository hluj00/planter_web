<?php

namespace App\Controller;

use App\Form\UserSettingsFormType;
use App\Repository\UserSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSettingsController extends BaseController
{
    /**
     * @Route("/user/settings", name="user_settings")
     */
    public function index(
        UserSettingsRepository $userSettingsRepository,
        Request $request
    ): Response
    {
        $userId = $this->getUser()->getId();
        $userSettings = $userSettingsRepository->findOneByUserId($userId);
        dump($userSettings);
        $form = $this->createForm(UserSettingsFormType::class, $userSettings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->get('send_notifications_at')->getData());
            dump($form->get('send_notifications')->getData());

            $userSettings->setSendNotificationsAt($form->get('send_notifications_at')->getData());
            $userSettings->setSendNotifications($form->get('send_notifications')->getData() ? 1 : 0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userSettings);
            $entityManager->flush();

        }


        return $this->render('user_settings/edit.html.twig', [
            'controller_name' => 'UserSettingsController',
            'form' => $form->createView(),
        ]);
    }
}
