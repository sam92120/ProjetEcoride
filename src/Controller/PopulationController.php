<?php

namespace App\Controller;
use App\Service\PopulationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class PopulationController extends AbstractController
{
    #[Route('/communes', name: 'communes_list')]
    public function index(Request $request, PopulationService $populationService): Response
    {
        $query = $request->query->get('q'); // récupère la recherche depuis GET
        $communes = $populationService->getCommunes(); // toutes les communes

        // Filtrer seulement si l'utilisateur a saisi une recherche
        if ($query) {
            $communes = array_filter($communes, fn($c) => stripos($c['nom'], $query) !== false);
        }

        return $this->render('communes/index.html.twig', [
            'communes' => $communes,
            'query' => $query,
        ]);
    }


    public function show(string $name, PopulationService $populationService): Response
    {
        $commune = $populationService->getCommuneByName($name);

        return $this->render('communes/show.html.twig', [
            'commune' => $commune,
        ]);
    }
}
