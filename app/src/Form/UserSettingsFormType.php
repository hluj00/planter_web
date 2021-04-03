<?php

namespace App\Form;

use App\Entity\UserSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $choices = [];
        for($i=0; $i<24 ; $i++){
            $date = new \DateTime('1970-01-01');
            $date->setTime($i,0,0);
            $choices[$i.':00'] = $date;
        }
        $date = new \DateTime('1970-01-01');
        $date->setTime(14,0,0);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            $time = $entity->getSendNotificationsAt();
            $choices = [];
            for($i=0; $i<24 ; $i++){
                $date = new \DateTime('1970-01-01');
                $date->setTime($i,0,0);
                $choices[$i.':00'] = $time->format("H") == $i ? $time : $date;
            }

            dump($entity);
            $form = $event->getForm();
            $form->add('send_notifications_at', ChoiceType::class, array(

                'choices' => $choices,
                'empty_data' => 'guide'
            ))
                ->add('send_notifications', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('save', SubmitType::class, ['label' => 'save']);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSettings::class,
        ]);
    }
}
