<?php

namespace App\Controller;

use App\Entity\UserSettings;
use App\Form\SelectPlanterFormType;
use App\Repository\PlanterRepository;
use App\Repository\UserRepository;
use App\Repository\UserSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class PairController extends BaseController
{
    private $planterRepository;
    private $userSettingsRepository;

    public function __construct(
        PlanterRepository $planterRepository,
        UserSettingsRepository $userSettingsRepository
    )
    {
        $this->planterRepository = $planterRepository;
        $this->userSettingsRepository = $userSettingsRepository;
    }

    /**
     * @Route("/pair", name="pair")
     * @return Response
     */
    public function index(Request $request): Response
    {
        $id = $request->get('id');
        dump($id);



        $planter = $this->planterRepository->findOneById($id);
        $planterId = is_null($planter) ? null : sprintf("planter%d",$planter->getId());
        $userHash = $this->getUser()->getHash();

        dump($planterId,$userHash);

        $userId = $this->getUser()->getId();
        $planters = $this->planterRepository->findByUserId($userId);
        $form = $this->createForm(SelectPlanterFormType::class, null,['trait_choices' => $planters,'data' => ['id' => $id]]);

        return $this->render('pair/index.html.twig', [
            'form' => $form->createView(),
            'planterId' => $planterId,
            'userHash' => $userHash,
        ]);
    }

}
