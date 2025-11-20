<?php

namespace App\DataFixtures;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CovoiturageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer les voitures et utilisateurs existants
        $voitures = $manager->getRepository(Voiture::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach (range(1, 10) as $i) {
            $covoiturage = new Covoiturage();

            $covoiturage->setDatedepart($faker->dateTimeBetween('+1 days', '+30 days'));
            $covoiturage->setLieudepart($faker->city);
            $covoiturage->setHeuredepart(\DateTime::createFromFormat('H:i', $faker->time('H:i')));
            $covoiturage->setDatearrive($faker->dateTimeBetween('+1 days', '+30 days'));
            $covoiturage->setHeurearrive(\DateTime::createFromFormat('H:i', $faker->time('H:i')));
            $covoiturage->setLieuarrivee($faker->city);
            $covoiturage->setPrixpersonne($faker->randomFloat(2, 5, 50));
            $covoiturage->setNbplace($faker->numberBetween(1, 4));

            // Choisir une voiture aléatoire
            $voiture = $faker->randomElement($voitures);

            // Si la voiture n'a pas de propriétaire, lui en affecter un au hasard
            if ($voiture->getProprietaire() === null) {
                $voiture->setProprietaire($faker->randomElement($users));
                $manager->persist($voiture);
            }

            $covoiturage->setVoiture($voiture);

            // Affecter le conducteur (= propriétaire de la voiture)
            $covoiturage->setUser($voiture->getProprietaire());

            // Ajouter 1 à 3 passagers (différents du conducteur)
            $randomUsers = $faker->randomElements($users, rand(1, 3));
            foreach ($randomUsers as $user) {
                // Éviter d’ajouter le conducteur comme passager
                if ($user !== $voiture->getProprietaire()) {
                    $covoiturage->addPassager($user);

                }
            }
            $covoiturage->setStatut('Disponible');
            $covoiturage->setActif(true);
            $manager->persist($covoiturage);
        }
        $manager->flush();
    }
}
