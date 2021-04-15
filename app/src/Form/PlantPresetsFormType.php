<?php

namespace App\Form;

use App\Entity\PlantPresets;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('light_duration', ChoiceType::class, array(
                'choices' => [
                    '1h' => 1,
                    '2h' => 2,
                    '3h' => 3,
                    '4h' => 4,
                    '5h' => 5,
                    '6h' => 6,
                    '7h' => 7,
                    '8h' => 8,
                    '9h' => 9,
                    '11h' => 11,
                    '12h' => 12,
                    '13h' => 13,
                    '14h' => 14,
                    '15h' => 15,
                    '16h' => 16,
                    '17h' => 17,
                    '18h' => 18,
                    ],
            ))
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
