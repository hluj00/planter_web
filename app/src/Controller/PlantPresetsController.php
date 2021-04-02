<?php

namespace App\Controller;

use App\Entity\PlantPresets;
use App\Entity\User;
use App\Form\PlantPresetsFormType;
use App\Repository\PlantPresetsRepository;
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
            'SettingsForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/plant-presets/edit/{id}", name="plant_presets_edit")
     */
    public function edit(Request $request, PlantPresetsRepository $settingsPlantRepository, $id): Response
    {
        $SettingsPlant = $settingsPlantRepository->findOneById($id);
        if ($SettingsPlant === null){
            return $this->renderUnauthorised();
            //todo neexistuje
        }else if (!$this->canEdit($this->getUser(), $SettingsPlant)){
            return $this->renderUnauthorised();
        }
        $form = $this->createForm(PlantPresetsFormType::class, $SettingsPlant)
            ->add( 'delete', SubmitType::class, ['label' => 'delete']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $delete = $form->get('delete')->isClicked();
            if ($delete){
                $entityManager->remove($SettingsPlant);
            }else{
                $SettingsPlant->setName($form->get('name')->getData());
                $SettingsPlant->setLightDuration($form->get('light_duration')->getData());
                $SettingsPlant->setLightLevel($form->get('light_level')->getData());
                $SettingsPlant->setMoisture($form->get('moisture')->getData());
                $SettingsPlant->setTemperature($form->get('temperature')->getData());
                $SettingsPlant->setUserId($this->getUser()->getId());
            }
            $entityManager->flush();

            return $this->redirectToRoute('plant_presets');
        }

        return $this->render('PlantPresets/edit.html.twig', [
            'SettingsForm' => $form->createView(),
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
