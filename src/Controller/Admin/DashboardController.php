<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Accueil;
use App\Entity\User;
use App\Entity\Covoiturage;
use App\Entity\Avis;
use App\Entity\Marque;
use App\Entity\Configuration;
use App\Entity\Role;
use App\Entity\Voiture;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private EntityManagerInterface $em
    ) {}

    public function index(): Response
    {
        /* ----------------------------------------------------
         * 1️⃣  STATISTIQUES
         * ---------------------------------------------------- */
        $usersCount = $this->em->getRepository(User::class)->count([]);
        $trajetsCount = $this->em->getRepository(Covoiturage::class)->count([]);
        $avisCount = $this->em->getRepository(Avis::class)->count([]);

        /* ----------------------------------------------------
         * 2️⃣  GRAPH : Trajets par mois
         * ---------------------------------------------------- */
        $conn = $this->em->getConnection();
        $sql = "
            SELECT MONTH(datedepart) AS mois, COUNT(*) AS total
            FROM covoiturage
            GROUP BY mois
            ORDER BY mois
        ";
        $result = $conn->executeQuery($sql)->fetchAllAssociative();

        $labels = [];
        $data = [];

        foreach ($result as $row) {
            $labels[] = date("F", mktime(0, 0, 0, $row['mois'], 1));
            $data[] = $row['total'];
        }

        $chartMonthly = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chartMonthly->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Trajets par mois',
                'data' => $data,
            ]]
        ]);

        /* ----------------------------------------------------
         * 3️⃣  GRAPH : Répartition des statuts
         * ---------------------------------------------------- */
        $statuses = ['Disponible', 'En cours', 'Terminé'];
        $statusValues = [];

        foreach ($statuses as $status) {
            $statusValues[] = $this->em->getRepository(Covoiturage::class)->count([
                'statut' => $status
            ]);
        }

        $chartStatus = $this->chartBuilder->createChart(Chart::TYPE_PIE);
        $chartStatus->setData([
            'labels' => $statuses,
            'datasets' => [[
                'label' => 'Répartition des statuts',
                'data' => $statusValues,
            ]]
        ]);

        /* ----------------------------------------------------
         * 4️⃣  ENVOI AU TEMPLATE
         * ---------------------------------------------------- */
        return $this->render('admin/dashboard.html.twig', [
            'users_count'     => $usersCount,
            'trajets_count'   => $trajetsCount,
            'avis_count'      => $avisCount,
            'chartMonthly'    => $chartMonthly,
            'chartStatus'     => $chartStatus,
            'current_menu_item' => 'Dashboard',
        
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ecoride')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin')
            ->setLocales([
                'fr' => '🇫🇷 Français',
                'en' => '🇬🇧 English',
                'pl' => '🇵🇱 Polski'
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Gestion des données');
        yield MenuItem::linkToCrud('Accueil', 'fas fa-home', Accueil::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Covoiturages', 'fas fa-car', Covoiturage::class);
        yield MenuItem::linkToCrud('Voitures', 'fas fa-car-side', Voiture::class);
        yield MenuItem::linkToCrud('Marques', 'fas fa-industry', Marque::class);
        yield MenuItem::linkToCrud('Configurations', 'fas fa-cogs', Configuration::class);
        yield MenuItem::linkToCrud('Rôles', 'fas fa-user-shield', Role::class);
        yield MenuItem::linkToCrud('Avis', 'fas fa-star', Avis::class);
    }
}
