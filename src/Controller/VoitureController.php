<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\VoitureForm;
use App\Entity\Voiture;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Marque;




final class VoitureController extends AbstractController
{
    #[Route('/voiture', name: 'app_voiture')]
    public function index(EntityManagerInterface $entityManagerInterface, VoitureRepository $voitureRepository): Response
    {

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('primary', 'Vous devez être connecté pour voir vos voitures.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur possède une voiture
        $voitureRepository = $entityManagerInterface->getRepository(Voiture::class);
        $existingVoiture = $voitureRepository->findOneBy(['proprietaire' => $user]);
        if (!$existingVoiture) {
            $this->addFlash('primary', 'Vous n\'avez pas encore enregistré de voiture.');
            return $this->redirectToRoute('app_voiture_new');
        }
        $voitures = $voitureRepository->findBy(['proprietaire' => $user]);
        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitures,
        ]);


    }
    #[Route('/voiture/new', name: 'app_voiture_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour enregistrer une voiture.');
            return $this->redirectToRoute('app_login');

        }


        // Vérifier si l'utilisateur possède déjà une voiture
        $voitureRepository = $entityManager->getRepository(Voiture::class);
        $existingVoiture = $voitureRepository->findOneBy(['proprietaire' => $user]);

        if ($existingVoiture) {
            $this->addFlash('error', 'Vous avez déjà enregistré une voiture.');
            return $this->redirectToRoute('app_covoiturage_new', ['id' => $existingVoiture->getId()]);
        }

        // Créer une nouvelle voiture
        $voiture = new Voiture();
        $voiture->setProprietaire($user);

        $form = $this->createForm(VoitureForm::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($voiture);
            $entityManager->flush();

            $this->addFlash('success', 'Voiture enregistrée avec succès.');
            return $this->redirectToRoute('app_covoiturage_new', ['id' => $voiture->getId()]);
        }

        return $this->render('voiture/new.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
        ]);
    }


    #[Route('/voiture/{id}/delete', name: 'app_voiture_delete')]



    #[Route('/voiture/{id}/edit', name: 'app_voiture_edit')]
    public function edit(int $id, VoitureRepository $voitureRepository, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $voiture = $voitureRepository->find($id);
        // Vérification si la voiture existe
        if (!$voiture) {
            throw $this->createNotFoundException('Voiture not found');
        }
        $form = $this->createForm(VoitureForm::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($voiture);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_voiture');
        }

        return $this->render('voiture/edit.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
        ]);
    }


}

