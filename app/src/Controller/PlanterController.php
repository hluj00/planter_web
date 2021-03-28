<?php

namespace App\Controller;

use App\Entity\Planter;
use App\Form\PlanterFormType;
use App\Repository\AirTemperatureRepository;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class PlanterController extends BaseController
{
    /**
     * @Route("/planter", name="planter")
     */
    public function index(PlanterRepository $planterRepository): Response
    {
        $userId = $this->getUser()->getId();

        $planters = $planterRepository->findByUserId($userId);

        return $this->render('planter/index.html.twig', [
            'controller_name' => 'PlanterController',
            'planters' => $planters,
        ]);
    }

    /**
     * @Route("/planter/new", name="planter_new")
     */
    public function new(Request $request, PlantPresetsRepository $plantPresetsRepository): Response
    {
        $userId = $this->getUser()->getId();
        $presets = $plantPresetsRepository->findByUserId($userId);
        $planter = new Planter();
        $form = $this->createForm(PlanterFormType::class, $planter, ['trait_choices' => $presets]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $planter->setName($form->get('name')->getData());
            $planter->setUserId($userId);
            $planter->setPlantPresetsId($form->get('plant_presets_id')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planter);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('planter');
        }

        return $this->render('planter/new.html.twig', [
            'PlanterForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/planter/detail/{id}", name="planter_detail")
     */
    public function detail(
        Request $request,
        PlantPresetsRepository $plantPresetsRepository,
        PlanterRepository $planterRepository,
        AirTemperatureRepository $airTemperatureRepository,
        $id): Response
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);


        $userId = $this->getUser()->getId();
        $presets = $plantPresetsRepository->findByUserId($userId);
        $planter = $planterRepository->findOneById($id);
        $form = $this->createForm(PlanterFormType::class, $planter, ['trait_choices' => $presets]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $planter->setName($form->get('name')->getData());
            $planter->setUserId($userId);
            $planter->setPlantPresetsId($form->get('plant_presets_id')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planter);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('planter');
        }

        $data =[];
        foreach ($airTemperatureRepository->findByPlanterId($id) as $airTemperature){
            $date =$airTemperature->getDate()->format('Y-m-d H:i:s');

            $data[] = [$date, $airTemperature->getValue()];
        }
        return $this->render('planter/detail.html.twig', [
            'PlanterForm' => $form->createView(),
            'data' => $serializer->serialize([
                [0, 0, 0],    [1, 10, 5],   [2, 23, 15],  [3, 17, 9],   [4, 18, 10],  [5, 9, 5],
                [6, 11, 3],   [7, 27, 19],  [8, 33, 25],  [9, 40, 32],  [10, 32, 24], [11, 35, 27],
                [12, 30, 22], [13, 40, 32], [14, 42, 34], [15, 47, 39], [16, 44, 36], [17, 48, 40],
                [18, 52, 44], [19, 54, 46], [20, 42, 34], [21, 55, 47], [22, 56, 48], [23, 57, 49],
                [24, 60, 52], [25, 50, 42], [26, 52, 44], [27, 51, 43], [28, 49, 41], [29, 53, 45],
                [30, 55, 47], [31, 60, 52], [32, 61, 53], [33, 59, 51], [34, 62, 54], [35, 65, 57],
                [36, 62, 54], [37, 58, 50], [38, 55, 47], [39, 61, 53], [40, 64, 56], [41, 65, 57],
                [42, 63, 55], [43, 66, 58], [44, 67, 59], [45, 69, 61], [46, 69, 61], [47, 70, 62],
                [48, 72, 64], [49, 68, 60], [50, 66, 58], [51, 65, 57], [52, 67, 59], [53, 70, 62],
                [54, 71, 63], [55, 72, 64], [56, 73, 65], [57, 75, 67], [58, 70, 62], [59, 68, 60],
                [60, 64, 56], [61, 60, 52], [62, 65, 57], [63, 67, 59], [64, 68, 60], [65, 69, 61],
                [66, 70, 62], [67, 72, 64], [68, 75, 67], [69, 80, 72]
            ],'json'),
            'data2' => $serializer->serialize($data, 'json'),
        ]);
    }
}
