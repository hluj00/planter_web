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
    private $userSettingsRepository;

    public function __construct(
        UserSettingsRepository $userSettingsRepository
    )
    {
        $this->userSettingsRepository = $userSettingsRepository;
    }

    /**
     * @Route("/user/settings", name="user_settings")
     */
    public function index(Request $request): Response
    {
        $userId = $this->getUser()->getId();
        $userSettings = $this->userSettingsRepository->findOneByUserId($userId);
        $form = $this->createForm(UserSettingsFormType::class, $userSettings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sendNotifications = $form->get('send_notifications')->getData() ? 1 : 0;

            $userSettings->setSendNotificationsAt($form->get('send_notifications_at')->getData());
            $userSettings->setSendNotifications($sendNotifications);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userSettings);
            $entityManager->flush();
            $this->addFlash('success', 'Updated.');
        }

        return $this->render('user_settings/edit.html.twig', [
            'controller_name' => 'UserSettingsController',
            'user_hash' => $this->getUser()->getHash(),
            'form' => $form->createView(),
        ]);
    }
}
