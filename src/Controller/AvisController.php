<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisForm;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class AvisController extends AbstractController
{
    #[Route('/avis', name: 'app_avis')]
    public function index(EntityManagerInterface $entityManagerInterface, AvisRepository $avisRepository): Response
    {
        $user = $this->getUser();
        //les conducteur + passager peuvent voir les avis ,
        if (!$this->isGranted('ROLE_CONDUCTEUR') && !$this->isGranted('ROLE_PASSAGER')) {
            $this->addFlash('error', 'Accès refusé. Vous devez être conducteur ou passager pour voir les avis.');
            return $this->redirectToRoute('app_accueil');
        }
        
        $avis = $avisRepository->findAll();
        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
            'user' => $user
        ]);
    }

    #[Route('/avis/create', name: 'app_avis_create')]
    public function create(EntityManagerInterface $entityManagerInterface, Request $request, AvisRepository $avisRepository): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisForm::class, $avis);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { // Check if the form is submitted and valid
            $entityManagerInterface->persist($avis);
            $entityManagerInterface->flush();
            // une fois le formulaire soumis et validé, je veux que l'utilisateur voit son commentaire
            // dans la liste des commentaires
            return $this->redirectToRoute('app_avis');
        }        // Render the form view
        return $this->render('avis/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }




    #[Route('/avis/delete/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManagerInterface, AvisRepository $avisRepository, Request $request): Response
    {
        $avis = $avisRepository->find($id);
        if ($avis) {
            $entityManagerInterface->remove($avis);
            $entityManagerInterface->flush();
        } else {
            $this->addFlash('error', 'Avis not found.');
        }
        return $this->redirectToRoute('app_avis');


    }



    #[Route('/avis/edit/{id}', name: 'app_avis_edit')]
    public function edit(int $id, EntityManagerInterface $entityManagerInterface, AvisRepository $avisRepository, Request $request): Response
    {
        $avis = $avisRepository->find($id);
        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');


        }
        $form = $this->createForm(AvisForm::class, $avis);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($avis);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_avis');
        }
        return $this->render('avis/edit.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
