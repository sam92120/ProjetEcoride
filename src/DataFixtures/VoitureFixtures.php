<?php

namespace App\DataFixtures;

use App\Entity\Voiture;
use App\Entity\Marque;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class VoitureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();
        $marques = $manager->getRepository(Marque::class)->findAll();

        if (empty($users) || empty($marques)) {
            throw new \Exception("Les utilisateurs et les marques doivent être présents avant d’ajouter des voitures.");
        }

        $energies = ['Essence', 'Diesel', 'Électrique', 'Hybride'];
        $couleurs = ['Rouge', 'Bleu', 'Noir', 'Blanc', 'Gris', 'Vert'];

        for ($i = 0; $i < 20; $i++) {
            $voiture = new Voiture();

            $voiture->setModele($faker->word() . ' ' . $faker->randomNumber(3))
                ->setImmatriculation(strtoupper($faker->bothify('??-###-??')))
                ->setEnergie($faker->randomElement($energies))
                ->setCouleur($faker->randomElement($couleurs))
                ->setDatePremiereImmatriculation($faker->dateTimeBetween('-15 years', 'now'))
                ->setProprietaire($faker->randomElement($users))
                ->setMarque($faker->randomElement($marques));

            $manager->persist($voiture);
        }

        $manager->flush();
    }
}
