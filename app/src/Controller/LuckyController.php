<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends BaseController
{
    /**
     * @Route("/lucky-number", name="lucky_number")
     */
    public function number(): Response
    {

        $number = random_int(0, 100);

        //         = new UserRepository();
//        $userRepo = $this->getDoctrine()->getRepository(User::class);
        return $this->render('number/number.html.twig', [
            'number' => $number,
            'uuid' => $this->getUser()->getId()
        ]);
    }
}