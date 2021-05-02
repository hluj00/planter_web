<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserSettings;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Time;

class RegistrationController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userName = $form->get('username')->getData();
            $duplicateUser = $this->userRepository->findOneByUsername($userName);
            if (is_null($duplicateUser)) {

                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setUsername($form->get('username')->getData());
                $user->setEmail($form->get('email')->getData());

                $user->setHash($this->generateUniqueHash());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $userId = $user->getId();


                $settings = new UserSettings($userId);
                $settings->setSendNotifications(false);
                $time = new \DateTime();
                $time->setTime(21, 0, 0);
                $settings->setSendNotificationsAt($time);
                $settings->setNotificationPeriodType(UserSettings::$PERIOD_LAST_24H);
                $entityManager->persist($settings);
                $entityManager->flush();
                return $this->redirectToRoute('app_login');
            }else{
                $form->get('username')->addError(new FormError('this username already exists'));
            }

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function generateUniqueHash(){
        do{
        $random = random_bytes(30);
        $hash = "user".md5($random);
        }while(!is_null($this->userRepository->findOneByHash($hash)));

        return $hash;
    }
}
