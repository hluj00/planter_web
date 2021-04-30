<?php

namespace App\Form;

use App\Entity\Planter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SelectPlanterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $presets =  $this->createOptionsFromPresets($options['trait_choices']);
        $dataId = $options['data']['id'];
        $builder
            ->setMethod('GET')
            ->add('id', ChoiceType::class, [
                'required' => false,
                'label' => 'planter',
                'choices' => $presets,
                'data' => $dataId,

            ])
        ;
    }

    private function createOptionsFromPresets( $planters): array
    {
        $option = [ "select planter" => ""];
        if (!empty($planters))
        foreach ($planters as $planter){
            $option[$planter->getName()] = $planter->getId();
        }
        return $option;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'trait_choices' => null,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return "";
    }

}
