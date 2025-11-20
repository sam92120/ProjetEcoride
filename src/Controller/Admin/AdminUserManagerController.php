<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\UserRoleManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserManagerController extends AbstractCrudController
{
    public function __construct(
        private UserRoleManager $roleManager,
        private AdminUrlGenerator $adminUrlGenerator,
        private RequestStack $requestStack,
        private EntityManagerInterface $em
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Active le rôle admin pour l'utilisateur sélectionné
     */
    #[Route('/admin/user/{id}/make-admin', name: 'admin_user_make_admin')]
    public function makeAdmin(int $id): RedirectResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('danger', 'Utilisateur introuvable');
            return $this->redirect($this->adminUrlGenerator->setController(self::class)->generateUrl());
        }

        $this->roleManager->makeAdmin  ($user);

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', sprintf('Rôle admin activé pour %s', $user->getPseudo()));

        return $this->redirect($this->adminUrlGenerator->setController(self::class)->generateUrl());
    }

    /**
     * Récupère l'utilisateur depuis la requête actuelle (optionnel)
     */
    private function getUserFromRequest(): ?User
    {
        $id = $this->requestStack->getCurrentRequest()?->query->get('entityId');
        if (!$id) return null;

        return $this->em->getRepository(User::class)->find($id);
    }
}
