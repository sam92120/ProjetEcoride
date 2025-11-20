<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('yvecametelus210@gmail.com');
        $user->setRoles(['ROLE_user']);
        $user->setNom('Metelus');
        $user->setPrenom('yveca');
        $user->setTelephone('0612435678');
        $user->setPassword(password_hash('Sam@92120', PASSWORD_BCRYPT));
        $user->setAdresse('45 Rue de la République, Lyon, France');

        $manager->persist($user);
        $manager->flush();


        // ✅ Référence enregistrée
        $this->addReference('user_yveca', $user);
    }
}
