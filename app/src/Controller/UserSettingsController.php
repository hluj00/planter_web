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
//        dump($userSettings);
        $form = $this->createForm(UserSettingsFormType::class, $userSettings);
        $form->handleRequest($request);

        dump($form->getErrors());
        if ($form->isSubmitted() && $form->isValid()) {
//            dump($form->get('send_notifications_at')->getData());
//            dump($form->get('send_notifications')->getData());

            $sendNotifications = $form->get('send_notifications')->getData() ? 1 : 0;
//            if($sendNotifications){
//            $form->addError(new F)
//            }

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
