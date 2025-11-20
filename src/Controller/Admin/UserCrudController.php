<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('pseudo', 'Pseudo'),
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            EmailField::new('Email', 'Email'),

            ArrayField::new('roles', 'Rôles'),

            BooleanField::new('isVerified', 'Compte vérifié'),

            TextField::new('telephone', 'Téléphone'),
            TextField::new('adresse', 'Adresse'),

            AssociationField::new('voitures', 'Voitures')->autocomplete(),
            AssociationField::new('covoiturages', 'Covoiturages')->autocomplete(),
        ];
    }
}
