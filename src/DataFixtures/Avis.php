<?php

namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Avis extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Tableau de commentaires différents
        $commentaires = [
            "Super service, merci !",
            "Très déçu, je ne recommande pas.",
            "Livraison rapide et efficace.",
            "Service client au top !",
            "Je n'ai pas encore testé.",
            "Parfait, je reviendrai.",
            "Moyen, peut mieux faire.",
        ];

        foreach ($commentaires as $commentaire) {
            $avis = new \App\Entity\Avis();
            $avis->setCommentaire($commentaire);
            $avis->setNote(rand(1, 5));
            $avis->setStatut("Publié");

            $manager->persist($avis);
        }

        $manager->flush();

        //Uncomment the above lines to create a sample Avis entity
    }

}
