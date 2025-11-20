<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Voiture;
use Dom\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VoitureForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'label_attr' => ['style' => 'color: black;'],
                'attr' => ['class' => 'form-control'], // Bootstrap style (optional)

            ])
            ->add('immatriculation', null, [
                'label_attr' => ['style' => 'color: black;'],
                'attr' => ['class' => 'form-control'], // Bootstrap style (optional)

            ])
            ->add('energie', ChoiceType::class, [
                'choices' => [
                    'Essence' => 'essence',
                    'Diesel' => 'diesel',
                    'Électrique' => 'electrique',
                    'Hybride' => 'hybride',
                ],
                'label' => 'Type d\'énergie',
                'label_attr' => ['style' => 'color: black;'],
                'attr' => ['class' => 'form-select'], // Bootstrap style (optional)
            ])
            ->add('couleur', ChoiceType::class, [
                'choices' => [
                    'Rouge' => 'rouge',
                    'Bleu' => 'bleu',
                    'Vert' => 'vert',
                    'Noir' => 'noir',
                    'Blanc' => 'blanc',
                    'Gris' => 'gris',
                    'Jaune' => 'jaune',
                    'Orange' => 'orange',
                    'Violet' => 'violet',
                    'Rose' => 'rose',
                    'Marron' => 'marron',
                    'Beige' => 'beige',
                    'Argent' => 'argent',
                    'Doré' => 'dore',

                ],
                'label' => 'Couleur',
                'label_attr' => ['style' => 'color: black;'],
                'attr' => ['class' => 'form-select'], // Bootstrap style (optional)
                'required' => true,
            ])
            ->add('datepremiereimmatriculation', \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de première immatriculation',
                'label_attr' => ['style' => 'color: black;'],
                'attr' => ['class' => 'form-control'], // Bootstrap style (optional)
                "required" => true,
            ])





            ->add('marque', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => \App\Entity\Marque::class,
                'choice_label' => 'libelle',
                'label' => 'Marque',
                'label_attr' => ['style' => 'color: black;'],
                'attr' => ['class' => 'form-select'],
                "required" => true,
            ])
            ->add('proprietaire', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => \App\Entity\User::class,
                'choice_label' => 'nom',
                'label' => 'Propriétaire',
                'label_attr' => ['style' => 'color: black;'],
                'attr' => ['class' => 'form-select'], // Bootstrap style (optional)
                "required" => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
