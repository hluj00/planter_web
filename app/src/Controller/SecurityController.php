<?php

namespace App\Controller;

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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $userRepository;
    private $passwordEncoder;
    private $authenticationUtils;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request): Response
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
                    $this->passwordEncoder->encodePassword(
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
