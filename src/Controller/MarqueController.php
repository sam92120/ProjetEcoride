<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\MarqueRepository;
use App\Entity\Marque;
use App\Form\MarqueForm;

final class MarqueController extends AbstractController
{
    #[Route('/marque', name: 'app_marque')]
    public function index(MarqueRepository $marqueRepository): Response
    {
        $marques = $marqueRepository->findAll();

        return $this->render('marque/index.html.twig', [
            'marques' => $marques,

        ]);
    }


    #[Route(path: '/marque/new', name: 'app_marque_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        // Create a new Marque entity and form
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour enregistrer une marque.');
            return $this->redirectToRoute('app_login');
        }
        //une seule marque doit être enregistrée
        $marqueRepository = $entityManagerInterface->getRepository(Marque::class);
        $existingMarque = $marqueRepository->findOneBy(['proprietaire' => $this->getUser()]);
        if ($existingMarque) {
            $this->addFlash('error', 'Vous avez déjà enregistré une marque.');
            return $this->redirectToRoute('app_voiture_new', ['id' => $existingMarque->getId()]);
        }

        $marque = new Marque();
        $form = $this->createForm(MarqueForm::class, $marque);
        $form->handleRequest($request);
        // Handle the form submission

        if ($form->isSubmitted() && $form->isValid()) {


            $marque->setProprietaire($this->getUser());
            $entityManagerInterface->persist($marque);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_voiture_new', [
                'id' => $marque->getId(),

            ]);


        }

        return $this->render('marque/new.html.twig', [
            'form' => $form->createView(),
            'marque' => $marque,

        ]);

    }

}