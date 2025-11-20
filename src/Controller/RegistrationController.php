<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {



        $user = new User();
        //ajouter du credit apres l'inscription
        $credit = 20; // Exemple de crédit initial
        // Assurez-vous que la méthode setCredit existe dans l'entité User

        $user->setCredit($credit);
        $user->setRoles(['ROLE_USER']); // Assurez-vous que l'utilisateur a le rôle approprié
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $plainPassword)
            );

            // ✅ Donne 20 crédits après validation
            $user->setCredit(20);

            // ✅ Ajoute le rôle
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $security->login($user, UsersAuthenticator::class, 'main');
        }


        return $this->render('inscription/inscription.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    #[Route('/inscription/terminer', name: 'app_register_complete')]
    public function registrationComplete(): Response
    {
        return $this->render('inscription/registration_complete.html.twig');

    }
    #[Route('/inscription/confirmation', name: 'app_register_confirmation')]
    public function registrationConfirmation(Request $request): Response
    {
        $form = $this->createForm(RegistrationForm::class);
        return $this->render('inscription/confirmation.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    #[Route('/inscription/erreur', name: 'app_register_error')]
    public function registrationError(): Response
    {
        return $this->render('inscription/error.html.twig');
    }
    #[Route('/inscription/connexion', name: 'app_register_login')]
    public function registrationLogin(): Response
    {
        return $this->render('inscription/login.html.twig');
    }
    #[Route('/inscription/connexion/terminer', name: 'app_register_login_complete')]
    public function registrationLoginComplete(): Response
    {
        return $this->render('inscription/login_complete.html.twig');



    }

    #[Route('/inscription/connexion/erreur', name: 'app_register_login_error')]
    public function registrationLoginError(): Response
    {
        return $this->render('inscription/login_error.html.twig');
    }
    #[Route('/inscription/connexion/confirmation', name: 'app_register_login_confirmation')]
    public function registrationLoginConfirmation(Request $request): Response
    {
        $form = $this->createForm(RegistrationForm::class);
        return $this->render('inscription/login_confirmation.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    //supprimer le compte
    #[Route('/inscription/supprimer', name: 'app_register_delete')]
    public function registrationDelete(): Response
    {
        return $this->render('inscription/delete.html.twig');
    }
}
