<?php

namespace App\Controller;

use App\Entity\PlantPresets;
use App\Entity\User;
use App\Entity\WaterLevel;
use App\Form\PlantPresetsFormType;
use App\Repository\LightLevelRepository;
use App\Repository\NotificationRepository;
use App\Repository\PlantPresetsRepository;
use App\Repository\WaterLevelRepository;
use DateInterval;
use DateTimeZone;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlantPresetsController extends BaseController
{
    /**
     * @Route("/plant-presets", name="plant_presets")
     * @param PlantPresetsRepository $settingsPlantRepository
     * @return Response
     */
    public function index(PlantPresetsRepository $settingsPlantRepository): Response
    {
        $settings = $settingsPlantRepository->findByUserId($this->getUser()->getId());

        return $this->render('PlantPresets/index.html.twig', [
            'settings' => $settings,
            'controller_name' => "pica"
        ]);
    }

    /**
     * @Route("/plant-presets/new", name="plant_presets_new")
     */
    public function new(Request $request): Response
    {
        $SettingsPlant = new PlantPresets();
        $form = $this->createForm(PlantPresetsFormType::class, $SettingsPlant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $SettingsPlant->setName($form->get('name')->getData());
            $SettingsPlant->setLightDuration($form->get('light_duration')->getData());
            $SettingsPlant->setLightLevel($form->get('light_level')->getData());
            $SettingsPlant->setMoisture($form->get('moisture')->getData());
            $SettingsPlant->setTemperature($form->get('temperature')->getData());
            $SettingsPlant->setUserId($this->getUser()->getId());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($SettingsPlant);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('plant_presets');
        }

        return $this->render('PlantPresets/new.html.twig', [
            'presetsForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/plant-presets/edit/{id}", name="plant_presets_edit")
     */
    public function edit(
        LightLevelRepository $lightLevelRepository,
        WaterLevelRepository $waterLevelRepository,
        NotificationRepository $notificationRepository,

        Request $request, PlantPresetsRepository $settingsPlantRepository, $id): Response
    {
        $plantPresets = $settingsPlantRepository->findOneById($id);
        if ($plantPresets === null){
            return $this->renderUnauthorised();
            //todo neexistuje
        }else if (!$this->canEdit($this->getUser(), $plantPresets)){
            return $this->renderUnauthorised();
        }
        $form = $this->createForm(PlantPresetsFormType::class, $plantPresets)
            ->add( 'delete', SubmitType::class, ['label' => 'delete']);
        $form->handleRequest($request);

//        =======================================================================
//        TEST

        $this->notificationAlreadyExists($notificationRepository, 2, 3);
//        $this->checkWaterLevel($waterLevelRepository, 1, $plantPresets);
//        =======================================================================

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $delete = $form->get('delete')->isClicked();
            if ($delete){
                $entityManager->remove($plantPresets);
            }else{
                $plantPresets->setName($form->get('name')->getData());
                $plantPresets->setLightDuration($form->get('light_duration')->getData());
                $plantPresets->setLightLevel($form->get('light_level')->getData());
                $plantPresets->setMoisture($form->get('moisture')->getData());
                $plantPresets->setTemperature($form->get('temperature')->getData());
                $plantPresets->setUserId($this->getUser()->getId());
            }
            $entityManager->flush();

            return $this->redirectToRoute('plant_presets');
        }

        return $this->render('PlantPresets/edit.html.twig', [
            'presetsForm' => $form->createView(),
            'plantPreset' => $plantPresets,
            'test' => $id
        ]);
    }

    private function renderUnauthorised(){
        return $this->render('general/unauthorised.html.twig', [
            'message' => 'You are not authorised to edit this :('
        ]);
    }


    private function canEdit(User $user, PlantPresets $settings): bool{
        return $user->getId() === $settings->getUserId();
    }

//============================================================================
//                                TEST
//============================================================================

    private function checkLight($lightLevelRepository, $planterId, PlantPresets $plantPresets){
        $from = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $from->sub(new DateInterval('P1D'));
        $from->setTime(0,0,0);
        $to = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $to->sub(new DateInterval('P1D'));
        $to->setTime(23,59,59);
        $data = $lightLevelRepository->findByPlanterIdAndDates($planterId, $from, $to);
        $minVaue = $plantPresets->getLightLevel();
        $minTime = $plantPresets->getLightDuration() * 3600;

        $size = sizeof($data);
        $timeTotal = 0;
        for ($i = 1; $i< $size;$i++){
            if ($data[$i-1]->getValue() + $data[$i-1]->getValue() > $minVaue * 2){
                $time1 = strtotime($data[$i-1]->getDate()->format('Y-m-d H:i:s'));
                $time2 = strtotime($data[$i]->getDate()->format('Y-m-d H:i:s'));
                $timeTotal += $time2 - $time1;
            }
        }

        dump($timeTotal);
        return($timeTotal < $minTime);
    }

    private function checkWaterLevel(WaterLevelRepository $waterLevelRepository,int $planterId, PlantPresets $plantPresets): bool
    {
        $minValue = 0.15;

        $from = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $from->sub(new DateInterval('PT1H'));
        $to = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $result = $waterLevelRepository->findByPlanterIdDatesAndValue($planterId, $from, $to, $minValue);
        dump($from, $to, $result);

        return !empty($result);
    }

    private function notificationAlreadyExists($notificationRepository, $userId, $notifType): bool
    {
        $date = new \DateTime('now', new DateTimeZone('Europe/Prague'));
        $date->setTime(0,0,0);
        $result = $notificationRepository->findByUserIdDateAndType($userId ,$date ,$notifType);
        dump($date);
        dump($result);
        return !empty($result);
    }
}
