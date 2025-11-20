<?php

namespace App\Controller;

use App\Entity\Accueil;
use App\Entity\User;
use App\Repository\AccueilRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AccueilFormType;



final class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function Accueil(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $accueil = new Accueil();

        return $this->render('accueil/index.html.twig', [
            //'form' => $accueil,
            'user' => $user,
        ]);

    }

    #[Route('/accueil/col', name: 'app_accueil_col')]
    public function col(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $accueil = new Accueil();

        return $this->render('accueil/colrow.html.twig', [
            //'form' => $accueil,
            'user' => $user,
        ]);
    } 


}



