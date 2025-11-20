<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Covoiturage;
use App\Entity\MonCompte;
use App\Entity\MonCompteRepository;
use App\Entity\User;
use App\Form\UserForm;
use App\Repository\CovoiturageRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


final class MonCompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function Inforpersonel(): Response
    {

        return $this->render('mon_compte/inforpersonnel.html.twig', [
            'info' => 'informationpersonel',
        ]);
    }


    #[Route('/compte/information', name: 'app_mon_compte_informationpersonnel')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        //$Compte = $userRepository->findAll();
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        $referer = $request->headers->get('referer');
        return $this->render('mon_compte/index.html.twig', [
            'monCompte' => [$user], // On l'encapsule dans un tableau pour garder le "for" dans Twig
            'backUrl' => $referer ?? $this->generateUrl('app_compte'),
        ]);
    }

    #[Route('/compte/covoiturage', name: 'app_mon_compte_covoiturage')]
    public function covoiturage(CovoiturageRepository $covoiturageRepository): Response
    {
        $CompteCovoiturage = $covoiturageRepository->findAll();
        return $this->render('mon_compte/covoiturage.html.twig', [
            'comptecovoiturage' => $CompteCovoiturage,
        ]);
    }

    #[Route('/compte/information', name: 'app_mon_compte_information')]
    public function information(): Response
    {

        return $this->render('mon_compte/information.html.twig', [
        ]);
    }
    #[Route('/compte/newcomplement', name: 'app_mon_compte_information_compteinformation')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser(); // récupère l'utilisateur connecté
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(MonCompteForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // pas besoin de persist() car c’est un objet existant
            $this->addFlash('success', 'Informations mises à jour.');
            return $this->redirectToRoute('app_mon_compte_information');
        }

        return $this->render('mon_compte/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
