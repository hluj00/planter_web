<?php

namespace App\Form;

use App\Entity\Planter;
use App\Entity\PlantPresets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $presets =  $this->createOptionsFromPresets($options['trait_choices']);
        $builder
            ->add('name')
            ->add('plant_presets_id', ChoiceType::class, [
                'label' => 'plant',
                'choices' => $presets
            ])
            ->add('color', ColorType::class)
            ->add( 'save', SubmitType::class, ['label' => 'save'])
        ;
    }

    private function createOptionsFromPresets( $presets): array
    {
        $option = [];
        foreach ($presets as $preset){
            $option[$preset->getName()] = $preset->getId();
        }
        return $option;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planter::class,
            'trait_choices' => null,
        ]);
    }
}
