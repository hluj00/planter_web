<?php

namespace App\Form;

use App\Entity\PlantPresets;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlantPresetsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new Regex([
                    'pattern' => '/^[A-Za-z]{2,20}$/',
                    'message' => "2-20 characters. Only letters numbers and -. "
                ])]
            ])
            ->add('moisture', NumberType::class, ['label' => 'moisture (%)'])
            ->add('temperature', TextType::class)
            ->add('light_level', TextType::class)
            ->add('light_duration', TextType::class)
            ->add( 'save', SubmitType::class, ['label' => 'save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlantPresets::class,
        ]);
    }
}
