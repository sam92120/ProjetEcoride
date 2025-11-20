<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MarqueFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();

        if (empty($users)) {
            throw new \Exception("Aucun utilisateur trouvé. Ajoute des utilisateurs avant les marques.");
        }

        $nomsMarques = [
            'Renault',
            'Peugeot',
            'Citroën',
            'Volkswagen',
            'Ford',
            'Toyota',
            'Nissan',
            'BMW',
            'Mercedes-Benz',
            'Audi',
            'Fiat',
            'Opel',
            'Kia',
            'Hyundai',
            'Mazda',
            'Subaru',
            'Honda',
            'Chevrolet',
            'Dacia',
            'Volvo',
            'Porsche',
            'Lexus',
            'Jaguar',
            'Land Rover',
            'Mitsubishi',
            'Suzuki',
            'Tesla',
            'Alfa Romeo',
            'Bentley',
            'Bugatti',
            'Ferrari',
            'Lamborghini',
            'McLaren',
            'Aston Martin',
            'Peugeot Sport',
            'Renault Sport',
            'Citroën Racing',
            'Ford Performance',
            'Toyota Gazoo Racing',
            'Nismo',
            'Mugen',
            'Subaru Tecnica International',
            'Honda Performance Development',
            'BMW ',
            'Mercedes',
            'Audi',
            'Volkswagen ',
            'Porsche ',
            'Fiat ',
            'Opel ',
            'Kia ',
            'Hyundai ',
            'Mazda ',
            'Subaru ',
            'Honda ',
            'Chevrolet ',
            'Dacia ',
            'Volvo ',
            'Porsche ',
            'Lexus ',
            'Jaguar ',
            'Land Rover ',
            'Mitsubishi ',
            'Suzuki ',
            'Tesla ',
            'Alfa Romeo ',
            'Bentley ',
            'Bugatti ',
            'Ferrari ',
            'Lamborghini ',
            'McLaren ',
            'Aston Martin ',
            'Peugeot Sport ',
            'Renault Sport ',


        ];

        foreach ($nomsMarques as $nom) {
            $marque = new Marque();
            $marque->setLibelle($nom);

            // Affecter un utilisateur propriétaire de la marque (logique à adapter)
            $proprietaire = $faker->randomElement($users);
            $marque->setProprietaire($proprietaire);

            $manager->persist($marque);
        }

        $manager->flush();
    }
}
