<?php

namespace App\Controller\Admin;

use App\Entity\Voiture;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class VoitureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Voiture::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('modele', 'Modèle'),
            TextField::new('immatriculation', 'Immatriculation'),
            TextField::new('energie', 'Énergie'),
            TextField::new('couleur', 'Couleur'),
            DateField::new('datePremiereImmatriculation', '1ère immatriculation'),

            AssociationField::new('proprietaire', 'Propriétaire')->autocomplete(),
            AssociationField::new('marque', 'Marque')->autocomplete(),
        ];
    }
}
