<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Entrez votre nom'
                ],
            ])
            ->add('surname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Entrez votre prénom'
                ],
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Age',
                'attr' => [
                    'placeholder' => 'Entrez votre age'
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez votre email'
                ],
            ])
            // Sélection du service
            ->add('service', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'nom',
                'label' => 'Service',
                'placeholder' => 'Choisissez un service',
                'mapped' => false
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de réservation',
                'placeholder' => 'Choisissez une date',
                'mapped' => false
            ])
            ->add('heure', ChoiceType::class, [
                'label' => 'Heure de réservation',
                'placeholder' => 'Choisissez une heure',
                'choices' => $this->ChoixHeure(),
                'mapped' => false
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function ChoixHeure(): array
    {
        $timeSlots = [];
        $startTime = new \DateTime('09:00');
        $endTime = new \DateTime('16:30');

        while ($startTime <= $endTime) {
            $timeSlots[$startTime->format('H:i')] = $startTime->format('H:i');
            $startTime->modify('+30 minutes');
        }

        return $timeSlots;
    }
}
