<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Planter;
use App\Entity\PlantPresets;
use App\Entity\UserSettings;
use App\Form\PlanterFormType;
use App\Repository\AirHumidityRepository;
use App\Repository\AirTemperatureRepository;
use App\Repository\LightLevelRepository;
use App\Repository\NotificationRepository;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
use App\Repository\SoilMoistureRepository;
use App\Repository\UserSettingsRepository;
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
    private $planterRepository;
    private $plantPresetsRepository;
    private $airTemperatureRepository;
    private $waterLevelRepository;
    private $notificationRepository;
    private $airHumidityRepository;
    private $lightLevelRepository;
    private $soilMoistureRepository;
    private $userSettingsRepository;

    public function __construct(
        PlanterRepository $planterRepository,
        PlantPresetsRepository $plantPresetsRepository,
        AirTemperatureRepository $airTemperatureRepository,
        WaterLevelRepository $waterLevelRepository,
        NotificationRepository $notificationRepository,
        AirHumidityRepository $airHumidityRepository,
        LightLevelRepository $lightLevelRepository,
        SoilMoistureRepository $soilMoistureRepository,
        UserSettingsRepository $userSettingsRepository
    )
    {
        $this->planterRepository = $planterRepository;
        $this->plantPresetsRepository = $plantPresetsRepository;
        $this->airTemperatureRepository = $airTemperatureRepository;
        $this->waterLevelRepository = $waterLevelRepository;
        $this->notificationRepository = $notificationRepository;
        $this->airHumidityRepository = $airHumidityRepository;
        $this->lightLevelRepository = $lightLevelRepository;
        $this->soilMoistureRepository = $soilMoistureRepository;
        $this->userSettingsRepository = $userSettingsRepository;
    }

    /**
     * @Route("/planter", name="planter")
     */
    public function index(): Response
    {
        $userId = $this->getUser()->getId();
        $userSetings = $this->userSettingsRepository->findOneByUserId($userId);



        $planters = $this->planterRepository->findByUserId($userId);
        $presets = $this->plantPresetsRepository->findByUserId($userId);
        $presetArray = [];
        $data = [];
        $notificationTime = $userSetings->getSendNotificationsAt()->format("hi");
        $now = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $now = $now->format("hi");



        foreach ($presets as $preset){
            $presetArray[$preset->getId()] = $preset;
        }

        foreach ($planters as $planter){
            $planterId = $planter->getId();
            $temp = $this->airTemperatureRepository->findLastInTenMinutes($planterId);
            $temp = isset($temp[0]) ? $temp[0]->getValue() : '--';
            $data[$planterId]['airTemperature'] = $temp;

            $water = $this->waterLevelRepository->findLastInTenMinutes($planterId);
            $water = isset($water[0]) ? round($water[0]->getValue()) : '--';
            $data[$planterId]['waterLevel'] = $water;

            $preset = $presetArray[$planter->getPlantPresetsId()];

            if ($now < $notificationTime) {
                $lightNotifications = $this->notificationRepository->findYesterdayNotifications($planterId, Notification::$TYPE_LIGHT_LEVEL);
            } else {
                $lightNotifications = $this->notificationRepository->findTodayNotifications($planterId, Notification::$TYPE_LIGHT_LEVEL);
            }



            $data[$planterId]['lowTemperature'] = ($temp != '--' && $preset->getTemperature() > $temp);
            $data[$planterId]['lowWater'] = ($temp != '--' &&  $water < 15);
            $data[$planterId]['lowLight'] =  !empty($lightNotifications) ;
        }



        return $this->render('planter/index.html.twig', [
            'controller_name' => 'PlanterController',
            'planters' => $planters,
            'presets' => $presetArray,
            'data' => $data
        ]);
    }

    /**
     * @Route("/planter/new", name="planter_new")
     */
    public function new(Request $request): Response
    {
        $userId = $this->getUser()->getId();
        $presets = $this->plantPresetsRepository->findByUserId($userId);
        $planter = new Planter();
        $form = $this->createForm(PlanterFormType::class, $planter, ['trait_choices' => $presets]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            if (!empty($this->planterRepository->findPlantersByUserIdAndName($userId,$name))){
                $this->addFlash('danger', 'You already have planter with this name.');
            }else{
                $planter->setName($name);
                $planter->setUserId($userId);
                $planter->setPlantPresetsId($form->get('plant_presets_id')->getData());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($planter);
                $entityManager->flush();
                $this->addFlash('success', 'Created.');

                $planterId = $planter->getId();
                return $this->redirectToRoute('pair', ['id' => $planterId]);
            }
        }

        return $this->render('planter/new.html.twig', [
            'planterForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/planter/detail/{planterId}", name="planter_detail")
     */
    public function detail($planterId): Response
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $from = new \DateTime();
        $from->modify('-5 days');

        $planter = $this->planterRepository->findOneById($planterId);

        $airTemperatureData = $this->mesuremetsToArray($this->airTemperatureRepository->findByPlanterIdAndDate($planterId, $from));
        $airHumidityData = $this->mesuremetsToArray($this->airHumidityRepository->findByPlanterIdAndDate($planterId, $from));
        $waterLevelData = $this->mesuremetsToArray($this->waterLevelRepository->findByPlanterIdAndDate($planterId, $from));
        $lightLevelData = $this->mesuremetsToArray($this->lightLevelRepository->findByPlanterIdAndDate($planterId, $from));
        $soilMoistureData = $this->mesuremetsToArray($this->soilMoistureRepository->findByPlanterIdAndDate($planterId, $from));

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
     * @Route("/planter/edit/{planterId}", name="planter_edit")
     */
    public function edit(Request $request, $planterId): Response
    {


        $userId = $this->getUser()->getId();
        $presets = $this->plantPresetsRepository->findByUserId($userId);
        $planter = $this->planterRepository->findOneById($planterId);
        $form = $this->createForm(PlanterFormType::class ,$planter, ['trait_choices' => $presets]);
        $form->add( 'delete', SubmitType::class, ['label' => 'delete']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $delete = $form->get('delete')->isClicked();
            if ($delete){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($planter);
                $this->addFlash('success', 'Deleted.');
                $entityManager->flush();
                return $this->redirectToRoute('planter');
            }else{
                $name = $form->get('name')->getData();
                if (!empty($this->planterRepository->findPlantersWithSameName($userId,$name, $planter->getId()))){
                    $this->addFlash('danger', 'You already have planter with this name.');
                }else{
                    $planter->setName($form->get('name')->getData());
                    $planter->setUserId($userId);
                    $planter->setPlantPresetsId($form->get('plant_presets_id')->getData());

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($planter);
                    $this->addFlash('success', 'Updated.');
                    $entityManager->flush();
                    return $this->redirectToRoute('planter');
                }
            }
        }


        return $this->render('planter/edit.html.twig', [
            'planterForm' => $form->createView(),
            'planter' => $planter,
        ]);
    }


}
