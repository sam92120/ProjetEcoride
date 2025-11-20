<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class AvisForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('commentaire')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'chauffeur' => 'chauffeur',
                    'passager' => 'passager',
                ],
                'label' => 'Statut',
                'label_attr' => ['style' => 'color: white;'],
                'attr' => ['class' => 'form-select'], // Bootstrap style (optionnel)
            ])
            ->add('note')
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '1 étoile' => 1,
                    '2 étoiles' => 2,
                    '3 étoiles' => 3,
                    '4 étoiles' => 4,
                    '5 étoiles' => 5,
                ],
                'label' => 'Note',
                'label_attr' => ['style' => 'color: white;'],
                'attr' => ['class' => 'form-select'], // Bootstrap style (optionnel)
            ])
            ->add('auteur', null, [
                'label' => 'Auteur',
                'label_attr' => ['style' => 'color: white;'],
                'attr' => ['class' => 'form-select'], // Bootstrap style (optionnel)
            ])

        ;

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
            'csrf_protection' => true, // Enable CSRF protection
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'avis_form', // Unique token ID for this form
        ])->setRequired([
                    'csrf_protection',
                    'csrf_field_name',
                    'csrf_token_id',
                ]);
    }
}
