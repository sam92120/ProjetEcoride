<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRoleManager
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Attribue le rôle ROLE_ADMIN à un utilisateur
     */
    public function makeAdmin(User $user): void
    {
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();
    }

    
    /**
     * Retire le rôle admin (repasser en ROLE_USER)
     */
    public function removeAdmin(User $user): void
    {
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);
        $this->em->flush();
    }
}
