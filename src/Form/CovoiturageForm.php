<?php

namespace App\Form;

use App\Entity\Covoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Voiture;
use DeepCopy\f001\B;
use Doctrine\DBAL\Types\BooleanType as TypesBooleanType;
use Symfony\Component\Form\Extension\Core\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CovoiturageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datedepart', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date de départ',
                'attr' => ['class' => 'form-control'],



            ])
            ->add('heuredepart', TimeType::class, [
                'widget' => 'choice',
                'html5' => true,
                'required' => true,
                'label' => 'Heure de départ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lieudepart', TextType::class, [
                'label' => 'Lieu de départ',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('datearrive', DateType::class, [
                'html5' => true,
                "required" => true,
                'label' => 'Date d\'arrivée',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('heurearrive', TimeType::class, [
                'widget' => 'choice',
                'html5' => true,
                "required" => true,
                'label' => 'Heure d\'arrivée',
                'attr' => ['class' => 'form-control'],


            ])
            ->add('lieuarrivee', TextType::class, [
                'label' => 'Lieu d\'arrivée',
                "required" => true,
                'attr' => ['class' => 'form-control'],

            ])


            ->add('nbplace', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nombre de places',
            ])
            ->add('prixpersonne', NumberType::class, [
                'label' => 'Prix par personne',
                'attr' => ['class' => 'form-control'],
                'html5' => true,
            ])


            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                //'choice_label' => 'immatriculation',
                //'label' => 'Voiture',
                //'attr' => ['class' => 'form-select'],
                //'placeholder' => ' Immatriculation',

            ])

                ->add('AccepteFumeur', ChoiceType::class, [
    'label' => 'Acceptez-vous une personne qui fume ?',
    'choices' => [
        'Oui' => true,
        'Non' => false,
    ],
    'expanded' => true, // radio buttons
    'multiple' => false, // une seule option peut être choisie
])

            ->add('AccepteAnimaux', ChoiceType::class, [
                'label' => 'Acceptez-vous les animaux ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true, // radio buttons
                'multiple' => false, // une seule option peut être choisie
            ]);

        //ajout des préférences utilisateur fumeur/non-fumeur, animaux acceptés ou non

        $builder->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'Email',
            'label' => 'Utilisateur',
            'attr' => ['class' => 'form-control '],
            'placeholder' => 'Sélectionnez un mail utilisateur',
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}
