<?php

namespace App\Form;

use App\Entity\Notification;
use App\Entity\UserSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class UserSettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('send_notifications', CheckboxType::class, [
            'required' => false,
        ])
            ->add('notification_period_type', ChoiceType::class, array(
//                    'expanded' => true,
                'choices' => [
                    "previous day" => UserSettings::$PERIOD_PREVIOUS_DAY,
                    "last 24 hours" => UserSettings::$PERIOD_LAST_24H,
                ],
            ))
            ->add('ifttt_endpoint', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}([-a-zA-Z0-9()@:%_\+.~#?&\/=]*)/',
                        'message' => "must be a valid URL"
                    ]),
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'save']);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            $time = $entity->getSendNotificationsAt();
            $choices = [];
            for($i=0; $i<24 ; $i++){
                $date = new \DateTime('1970-01-01');
                $date->setTime($i,0,0);
                $choices[$i.':00'] = $time->format("H") == $i ? $time : $date;
            }

            $form = $event->getForm();
            $form->
                add('send_notifications_at', ChoiceType::class, array(
                    'choices' => $choices,
                    'empty_data' => 'guide'
                ));

        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $sendNotif = ($form->get('send_notifications')->getData());
            $url = ($form->get('ifttt_endpoint')->getData());
            if ($sendNotif && empty($url)){
                $form->get("ifttt_endpoint")->addError(new FormError('Field error!'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSettings::class,
        ]);
    }
}
