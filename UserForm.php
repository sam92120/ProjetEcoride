<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        
        $builder

            ->add('nom')
            ->add('prenom')
            ->add('Email')
            //->add('role')
            ->add('motdepasse', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'required' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ])
            ->add('telephone')
            ->add('adresse')
            //->add('username')
            ->add('photo', FileType::class, [
                'label' => 'Profile Picture',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)',
                    ]),
                ],
                'help' => 'Please upload a valid image (JPEG, PNG, GIF)',

            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
