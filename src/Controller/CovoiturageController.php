<?php

namespace App\Controller;

use App\Repository\CovoiturageRepository;
use App\Repository\VoitureRepository;
use App\Entity\Covoiturage;
use App\Entity\User;
use App\Entity\Voiture;
use App\Entity\Avis;
use App\Form\CovoiturageForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;


use function Symfony\Component\String\u;

final class CovoiturageController extends AbstractController
{

#[Route('/covoiturage', name: 'app_covoiturage')]
public function index(Request $request, CovoiturageRepository $covoiturageRepository): Response
{
    $session = $request->getSession();
    $user = $this->getUser();

    // --- GESTION DU MODE ---
    // Si l'utilisateur a cliqué sur un bouton : ?mode=passager ou ?mode=conducteur
    if ($request->query->has('mode')) {
        $session->set('mode_covoiturage', $request->query->get('mode'));
    }

    // Récupérer le mode ou mettre "passager" par défaut
    $mode = $session->get('mode_covoiturage', 'passager');


    // --- LECTURE DES FILTRES ---
    $filters = [
        'depart' => $request->query->get('depart') ?? '',
        'arrivee' => $request->query->get('arrivee') ?? '',
        'date' => $request->query->get('date_depart') ? new \DateTime($request->query->get('date_depart')) : null,
        'nbplace' => $request->query->get('nbplace') ?? null,
    ];

    // --- MODE PASSAGER ---
    if ($mode === 'passager') {

        $covoiturages = $covoiturageRepository
            ->searchQueryBuilder(
                $filters['depart'],
                $filters['arrivee'],
                $filters['date'] ?? new \DateTime()
            )
            ->getQuery()
            ->getResult();

    }
    // --- MODE CONDUCTEUR ---
    else {

        $covoiturages = $covoiturageRepository
            ->getActifsByConducteur($user, $filters)
            ->getQuery()
            ->getResult();
    }

    return $this->render('covoiturage/index.html.twig', [
        'covoiturages' => $covoiturages,
        'mode' => $mode,
        'filters' => $filters,
        'user' => $user,
    ]);
}


    
    #[Route('/search', name: 'app_accueil_search')]
public function search(Request $request, CovoiturageRepository $repo): Response
{
    $depart = $request->query->get('depart');
    $arrivee = $request->query->get('arrivee');
    $date = $request->query->get('date_depart') ? new \DateTime($request->query->get('date_depart')) : null;

    $covoiturages = $repo->search($depart, $arrivee, $date);

    return $this->render('accueil/search.html.twig', [
        'covoiturages' => $covoiturages,
        'depart' => $depart,
        'arrivee' => $arrivee,
        'date' => $date,
    ]);
}



    #[Route('/covoiturage/switch-role', name: 'app_covoiturage_switch_role')]
    public function switchRole(Request $request): Response
    {
        $session = $request->getSession();
        $modeActuel = $session->get('mode_covoiturage', 'passager');
        $nouveauMode = $modeActuel === 'passager' ? 'conducteur' : 'passager';
        $session->set('mode_covoiturage', $nouveauMode);

        $this->addFlash('success', 'Mode changé en ' . ucfirst($nouveauMode));
        return $this->redirectToRoute('app_covoiturage');
    }

    #[Route('/covoiturage/information/{id}', name: 'app_covoiturage_information')]
    public function information(Covoiturage $covoiturage): Response
    {
        $voiture = $covoiturage->getVoiture();
        $avis = new Avis();
        $avis->setNote(5);
        $avis->setCommentaire("Conducteur très sympathique et ponctuel.");
        $user = $covoiturage->getUser();

        return $this->render('covoiturage/information.html.twig', [
            'covoiturage' => $covoiturage,
            'avis' => $avis,
            'voiture' => $voiture,
            'user' => $user,
        ]);
    }

   
    #[Route('/covoiturage/reserver/{id}', name: 'app_covoiturage_reserver')]
    public function reserver(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver.');
            return $this->redirectToRoute('app_login');
        }
 //vérifier si l'utilisateur est le conducteur
        if ($covoiturage->getUser() === $user) {
            $this->addFlash('warning', 'Vous ne pouvez pas réserver votre propre covoiturage.');
            return $this->redirectToRoute('app_covoiturage');
        }
        

        if ($covoiturage->getPassagers()->contains($user)) { 
            
            $this->addFlash('warning', 'Vous avez déjà réservé ce covoiturage.');
            return $this->redirectToRoute('app_covoiturage');
        }

        if ($covoiturage->getNbplace() < 0) {
            $this->addFlash('primary', 'Aucune place disponible pour ce trajet!');
            return $this->redirectToRoute('app_covoiturage');
        }

        $covoiturage->addPassager($user);
        $covoiturage->setNbplace($covoiturage->getNbplace() - 1);
        $em->persist($covoiturage);
        $em->flush();

        $this->addFlash('success', 'Réservation confirmée !');
        return $this->redirectToRoute('app_mes_reservations');
    }

    #[Route('/covoiturage/annuler/{id}', name: 'app_covoiturage_annuler', methods: ['GET'])]
    public function annulerReservation(Request $request, Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        if ($covoiturage->getPassagers()->contains($user)) {
            $covoiturage->removePassager($user);
            $covoiturage->setNbplace($covoiturage->getNbplace() + 1);
            $em->persist($covoiturage);
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        } else {
            $this->addFlash('warning', 'Vous n’avez pas réservé ce covoiturage.');
        }

        return $this->redirectToRoute('app_covoiturage');
    }

    #[Route('/covoiturage/mesreservations', name: 'app_mes_reservations')]
    public function mesReservations(CovoiturageRepository $repo, EntityManagerInterface $em): Response
    {   //seul un conducteur connecté peut accéder à ses réservations
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('success', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }
        $allReservations = $repo->findAll();
        $trajetsConduits = [];
        $trajetsReserves = [];

        foreach ($allReservations as $reservation) {
            if ($reservation->getUser() === $user ) {
            
                $trajetsConduits[] = $reservation;
            } elseif ($reservation->getPassagers()->contains($user)) { 
                $trajetsReserves[] = $reservation;
            }
        }
        //dd(  $trajetsConduits);

        return $this->render('covoiturage/mesreservations.html.twig', [
            'trajetsConduits' => $trajetsConduits, 
            'trajetsReserves' => $trajetsReserves,
            
        ]);
    }

    #[Route('/covoiturage/historique', name: 'app_covoiturage_historique')]
    public function historique(CovoiturageRepository $repo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('success', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $historique = $repo->getQueryBuilderHistoriqueByUser($user)->getQuery()->getResult();
        return $this->render('covoiturage/historique.html.twig', [
            'historique' => $historique,
        ]);
    }

    
    
#[Route('/covoiturage/demarrer/{id}', name: 'app_covoiturage_demarrer_course')]
public function demarrerCourse(Covoiturage $covoiturage, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('success', 'Vous devez être connecté pour démarrer une course!');
        return $this->redirectToRoute('app_login');
    }

    // Seul le conducteur peut démarrer la course
    if ($covoiturage->getUser() !== $user) {
        $this->addFlash('danger', 'Seul le conducteur peut démarrer la course.');
        return $this->redirectToRoute('app_mes_reservations');
    }

    // Démarrage du covoiturage
    $covoiturage->demarrer();
    $em->flush();

    $this->addFlash('success', 'La course a démarré !');

    return $this->redirectToRoute('app_mes_reservations');
}



#[Route('/covoiturage/arreter/{id}', name: 'app_covoiturage_arreter_course')]
public function arreterCourse(Covoiturage $covoiturage, EntityManagerInterface $em, NotifierInterface $notifier): Response

{   

    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('primary', 'Vous devez être connecté pour arrêter une course.');
        return $this->redirectToRoute('app_login');
    }

    //on peut pas annuler une course en cours
    if ($covoiturage->getStatut() !== 'En cours') {
        $this->addFlash('danger', 'Vous ne pouvez pas arrêter une course qui n\'est pas en cours.');
        return $this->redirectToRoute('app_mes_reservations');
    }

    //seul le conducteur peut arrêter la course
    if ($covoiturage->getUser() !== $user) {
        $this->addFlash('danger', 'Seul le conducteur peut arrêter la course.');
        return $this->redirectToRoute('app_covoiturage_historique');
    }
    
    //supprimer la course
    $covoiturage->arreter();
    $em->persist($covoiturage);
    $em->flush();


    $this->addFlash('success', 'La course est maintenant terminée.');

    /*une notification peut être ajoutée ici pour informer 
    les passagers de la fin de la course et de mettre une note */
        // --- Notification aux passagers ---
         foreach ($covoiturage->getPassagers() as $passager) {
        $notification = new Notification(
            'Merci pour votre trajet ! Vous pouvez maintenant laisser une note.',
            ['browser'] // tu peux mettre 'email', 'sms', etc. selon ton canal
        );
        $notifier->send($notification, new Recipient($passager->getEmail()));
    }


    return $this->redirectToRoute('app_covoiturage_historique');
}

//ajouter un nouveau covoiturage

#[Route('/covoiturage/new', name: 'app_covoiturage_new')]
public function nouveauCovoiturage(Request $request, EntityManagerInterface $em): Response
{ 
    $user = $this->getUser();
    if (!$user) {
        $this->addFlash('primary', 'Vous devez être connecté pour ajouter un covoiturage.');
        return $this->redirectToRoute('app_login');
    }
    
   //verifier si l'utilisateur a  deja un covoiturage actif
  //bloquer la création de covoiturage il a deja covoiturage actif

   $existingCovoiturage = $em->getRepository(Covoiturage::class)->findOneBy([
       'user' => $user,
       'actif' => true,
   ]);
   if ($existingCovoiturage) {
       $this->addFlash('danger', 'Vous avez déjà un covoiturage actif.');
       return $this->redirectToRoute('app_covoiturage');
   }
      
   //un passager ne peut pas creer un covoiturage

   if ($user->getRoles() && in_array('ROLE_PASSAGER', $user->getRoles())) {
       $this->addFlash('danger', 'Les passagers ne peuvent pas créer de covoiturage.');
       return $this->redirectToRoute('app_covoiturage');
   } 
    // création normale
    $covoiturage = new Covoiturage();//instanciation de l'entité covoiturage
    $form = $this->createForm(CovoiturageForm::class, $covoiturage);//création du formulaire

    $form->handleRequest($request);//gestion de la requête

    if ($form->isSubmitted() && $form->isValid()) {//vérification de la soumission et de la validité du formulaire
        $covoiturage->setUser($user);//association du covoiturage à l'utilisateur connecté
        $em->persist($covoiturage);//persistance de l'entité covoiturage
        $em->flush();//exécution des opérations en base de données

        $this->addFlash('primary', 'Covoiturage créé avec succès !');//message de succès
        return $this->redirectToRoute('app_covoiturage');//redirection vers la liste des covoiturages
    }

    return $this->render('covoiturage/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

}   
