<?php

namespace App\Controller;

use App\Entity\PlantPresets;
use App\Entity\User;
use App\Entity\WaterLevel;
use App\Form\PlantPresetsFormType;
use App\Repository\LightLevelRepository;
use App\Repository\NotificationRepository;
use App\Repository\PlanterRepository;
use App\Repository\PlantPresetsRepository;
use App\Repository\SoilMoistureRepository;
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
    private $planterRepository;
    private $plantPresetsRepository;

    public function __construct(
        PlantPresetsRepository $plantPresetsRepository,
        PlanterRepository $planterRepository
    )
    {
        $this->planterRepository = $planterRepository;
        $this->plantPresetsRepository = $plantPresetsRepository;
    }

    /**
     * @Route("/plant-presets", name="plant_presets")
     * @return Response
     */
    public function index(): Response
    {
        $settings = $this->plantPresetsRepository->findByUserId($this->getUser()->getId());

        return $this->render('PlantPresets/index.html.twig', [
            'settings' => $settings,
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
    public function edit(Request $request, $id): Response
    {
        $plantPreset = $this->plantPresetsRepository->findOneById($id);
        if ($plantPreset === null){
            return $this->renderUnauthorised();
            //todo neexistuje
        }else if (!$this->canEdit($this->getUser(), $plantPreset)){
            return $this->renderUnauthorised();
        }
        $form = $this->createForm(PlantPresetsFormType::class, $plantPreset)
            ->add( 'delete', SubmitType::class, ['label' => 'delete']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $delete = $form->get('delete')->isClicked();
            if ($delete){
                if (!empty($this->planterRepository->findByPlantPresetId($plantPreset->getId()))){
                    $this->addFlash('danger', 'can\'t delete. some planter is using this');
                }else{
                    $entityManager->remove($plantPreset);
                    $entityManager->flush();
                    $this->addFlash('success', 'Deleted.');
                }

            }else{
                $plantPreset->setName($form->get('name')->getData());
                $plantPreset->setLightDuration($form->get('light_duration')->getData());
                $plantPreset->setLightLevel($form->get('light_level')->getData());
                $plantPreset->setMoisture($form->get('moisture')->getData());
                $plantPreset->setTemperature($form->get('temperature')->getData());
                $plantPreset->setUserId($this->getUser()->getId());
                $entityManager->flush();

                $this->addFlash('success', 'Updated.');
            }



            return $this->redirectToRoute('plant_presets');
        }

        return $this->render('PlantPresets/edit.html.twig', [
            'presetsForm' => $form->createView(),
            'plantPreset' => $plantPreset,
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

}
