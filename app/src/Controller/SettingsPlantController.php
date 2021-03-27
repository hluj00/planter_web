<?php

namespace App\Controller;

use App\Entity\SettingsPlant;
use App\Entity\User;
use App\Form\SettingsPlantFormType;
use App\Repository\SettingsPlantRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsPlantController extends BaseController
{
    /**
     * @Route("/settings-plant", name="settings_plant")
     * @param SettingsPlantRepository $settingsPlantRepository
     * @return Response
     */
    public function index(SettingsPlantRepository $settingsPlantRepository): Response
    {
        $settings = $settingsPlantRepository->findByUserId($this->getUser()->getId());

        return $this->render('settings_plant/index.html.twig', [
            'settings' => $settings,
            'controller_name' => "pica"
        ]);
    }

    /**
     * @Route("/settings-plant/new", name="settings_plant_new")
     */
    public function new(Request $request): Response
    {
        $SettingsPlant = new SettingsPlant();
        $form = $this->createForm(SettingsPlantFormType::class, $SettingsPlant);
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

            return $this->redirectToRoute('settings_plant');
        }

        return $this->render('settings_plant/new.html.twig', [
            'SettingsForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/settings-plant/edit/{id}", name="settings_plant_edit")
     */
    public function edit(Request $request, SettingsPlantRepository $settingsPlantRepository, $id): Response
    {
        $SettingsPlant = $settingsPlantRepository->findOneByIdAndUserId($id);
        if ($SettingsPlant === null){
            return $this->renderUnauthorised();
            //todo neexistuje
        }else if (!$this->canEdit($this->getUser(), $SettingsPlant)){
            return $this->renderUnauthorised();
        }
        $form = $this->createForm(SettingsPlantFormType::class, $SettingsPlant)
            ->add( 'delete', SubmitType::class, ['label' => 'delete']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $delete = $form->get('delete')->isClicked();
            dump('kokos');
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
            // do anything else you need here, like send an email

            return $this->redirectToRoute('settings_plant');
        }

        return $this->render('settings_plant/edit.html.twig', [
            'SettingsForm' => $form->createView(),
            'test' => $id
        ]);
    }

    private function renderUnauthorised(){
        return $this->render('general/unauthorised.html.twig', [
            'message' => 'You are not authorised to edit this :('
        ]);
    }


    private function canEdit(User $user,SettingsPlant $settings): bool{
        return $user->getId() === $settings->getUserId();
    }
}
