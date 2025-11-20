<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPhotoForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'mapped' => false,      // IMPORTANT : on ne lie PAS ce champ à la propriété User::photo
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg, image/png, image/gif',
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG, PNG, GIF)',
                    ]),
                ],
            ])
            ->add('removePhoto', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
                'required' => false,
                'label' => 'Supprimer la photo actuelle',
            ])
            ->add("telephone", \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => 'Téléphone',
            ])
            ->add('adresse', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => 'Adresse',
            ]);
        // Pas besoin de lier à une classe, car photo est unmapped
        // On n'a pas besoin de configureOptions ici car on ne lie pas à une ent
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Pas besoin de lier à une classe, car photo est unmapped
        $resolver->setDefaults([]);
    }
}
