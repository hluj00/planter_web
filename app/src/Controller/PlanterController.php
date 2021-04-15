<?php

namespace App\Controller;

use App\Entity\Planter;
use App\Entity\PlantPresets;
use App\Form\PlanterFormType;
use App\Repository\AirHumidityRepository;
use App\Repository\AirTemperatureRepository;
use App\Repository\LightLevelRepository;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
use App\Repository\SoilMoistureRepository;
use App\Repository\WaterLevelRepository;
use DateInterval;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
    public function index(PlanterRepository $planterRepository, PlantPresetsRepository $plantPresetsRepository): Response
    {
        $userId = $this->getUser()->getId();

        $planters = $planterRepository->findByUserId($userId);
        $presets = $plantPresetsRepository->findByUserId($userId);
        $presetArray = [];
        foreach ($presets as $preset){
            $presetArray[$preset->getId()] = $preset;
        }

//        dump($presetArray);
        return $this->render('planter/index.html.twig', [
            'controller_name' => 'PlanterController',
            'planters' => $planters,
            'presets' => $presetArray,
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
        AirHumidityRepository $airHumidityRepository,
        WaterLevelRepository $waterLevelRepository,
        LightLevelRepository $lightLevelRepository,
        SoilMoistureRepository $soilMoistureRepository,
        $id): Response
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);



        $userId = $this->getUser()->getId();
        $planter = $planterRepository->findOneById($id);
        $planterId = $planter->getId();


        $from = new \DateTime();
        $from->sub(new DateInterval('PT1H'));
        $from->setTime(0,0,0);
        $to = $from;
        $to->setTime(23,59,59);
//        dump($airTemperatureRepository->findByPlanterIdDateAndValue($planterId, $from, $to, 10));


        $airHumidityData = $this->mesuremetsToArray($airTemperatureRepository->findByPlanterId($id));
        $airTemperatureData = $this->mesuremetsToArray($airHumidityRepository->findByPlanterId($id));
        $waterLevelData = $this->mesuremetsToArray($waterLevelRepository->findByPlanterId($id));
        $lightLevelData = $this->mesuremetsToArray($lightLevelRepository->findByPlanterId($id));
        $soilMoistureData = $this->mesuremetsToArray($soilMoistureRepository->findByPlanterId($id));

        return $this->render('planter/detail.html.twig', [
            'planter' => $planter,
            'airHumidityData' => $serializer->serialize($airHumidityData, 'json'),
            'airTemperatureData' => $serializer->serialize($airTemperatureData, 'json'),
            'waterLevelData' => $serializer->serialize($waterLevelData, 'json'),
            'lightLevelData' => $serializer->serialize($lightLevelData, 'json'),
            'soilMoistureData' => $serializer->serialize($soilMoistureData, 'json'),
        ]);
    }

    private function mesuremetsToArray($mesurements): array
    {
        $data = [];
        foreach ($mesurements as $airTemperature){
            $date =$airTemperature->getDate()->format('Y-m-d H:i:s');

            $data[] = [$date, $airTemperature->getValue()];
        }
        return $data;
    }

    /**
     * @Route("/planter/edit/{id}", name="planter_edit")
     */
    public function edit(
        LightLevelRepository $LightLevelRepository,

        Request $request,
        PlantPresetsRepository $plantPresetsRepository,
        PlanterRepository $planterRepository,
        $id): Response
    {
        $userId = $this->getUser()->getId();
        $planter = $planterRepository->findOneById($id);
        $planterId = $planter->getId();


        $from = new \DateTime();
        $from->sub(new DateInterval('PT1H'));
        $from->setTime(0,0,0);
        $to = $from;
        $to->setTime(23,59,59);


        $userId = $this->getUser()->getId();
        $presets = $plantPresetsRepository->findByUserId($userId);
        $planter = $planterRepository->findOneById($id);
        $form = $this->createForm(PlanterFormType::class, $planter, ['trait_choices' => $presets]);
        $form->add( 'delete', SubmitType::class, ['label' => 'delete']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $delete = $form->get('delete')->isClicked();
            if ($delete){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($planter);
            }else{
                $planter->setName($form->get('name')->getData());
                $planter->setUserId($userId);
                $planter->setPlantPresetsId($form->get('plant_presets_id')->getData());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($planter);
            }
            $entityManager->flush();

            return $this->redirectToRoute('planter');
        }


        return $this->render('planter/edit.html.twig', [
            'PlanterForm' => $form->createView(),
            'planterName' => $planter->getName(),
        ]);
    }


}
