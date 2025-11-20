<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    /**
     * Recherche passager : covoiturages actifs selon les filtres
     */
    public function searchQueryBuilder(string $depart, string $arrivee, \DateTime $date)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.actif = true')
            ->andWhere('LOWER(c.lieudepart) LIKE :depart')
            ->andWhere('LOWER(c.lieuarrivee) LIKE :arrivee')
            ->setParameter('depart', '%' . strtolower($depart) . '%')
            ->setParameter('arrivee', '%' . strtolower($arrivee) . '%')
            ->orderBy('c.datedepart', 'ASC');

        return $qb;
    }

    /**
     * Mode conducteur : afficher uniquement les trajets du conducteur connecté
     */
    public function getActifsByConducteur(User $user, ?array $filters = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.actif = true')
            ->andWhere('c.statut != :termine')
            ->setParameter('user', $user)
            ->setParameter('termine', 'Terminé');

        if ($filters) {
            if (!empty($filters['depart'])) {
                $qb->andWhere('LOWER(c.lieudepart) LIKE :depart')
                ->setParameter('depart', '%' . strtolower($filters['depart']) . '%');
            }
            if (!empty($filters['arrivee'])) {
                $qb->andWhere('LOWER(c.lieuarrivee) LIKE :arrivee')
                ->setParameter('arrivee', '%' . strtolower($filters['arrivee']) . '%');
            }
            if (!empty($filters['date'])) {
                $start = (clone $filters['date'])->setTime(0,0);
                $end = (clone $filters['date'])->setTime(23,59,59);
                $qb->andWhere('c.datedepart BETWEEN :start AND :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
            }
        }

        return $qb->orderBy('c.datedepart', 'ASC');
    }

    /**
     * Ancienne version (passager) conservée pour compatibilité
     */
    public function getQueryBuilderAllActifs(?array $filters = null, ?string $sortField = null, ?string $sortDirection = 'ASC')
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.actif = true')
            ->andWhere('c.statut != :termine')
            ->andWhere('c.nbplace > 0')
            ->setParameter('termine', 'Terminé');

        if ($filters) {
            if (!empty($filters['depart'])) {
                $qb->andWhere('LOWER(c.lieudepart) LIKE :depart')
                   ->setParameter('depart', '%' . strtolower($filters['depart']) . '%');
            }
            if (!empty($filters['arrivee'])) {
                $qb->andWhere('LOWER(c.lieuarrivee) LIKE :arrivee')
                   ->setParameter('arrivee', '%' . strtolower($filters['arrivee']) . '%');
            }
            if (!empty($filters['date'])) {
                $start = (clone $filters['date'])->setTime(0,0);
                $end = (clone $filters['date'])->setTime(23,59,59);
                $qb->andWhere('c.datedepart BETWEEN :start AND :end')
                   ->setParameter('start', $start)
                   ->setParameter('end', $end);
            }
        }

        // Tri
        $allowedSorts = ['prixpersonne', 'datedepart', 'nbplace'];
        if ($sortField && in_array($sortField, $allowedSorts)) {
            $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';
            $qb->orderBy('c.' . $sortField, $sortDirection);
        } else {
            $qb->orderBy('c.datedepart', 'ASC');
        }

        return $qb;
    }

    /**
     * Covoiturages où l'utilisateur est conducteur
     */
    public function getQueryBuilderByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.datedepart', 'DESC');
    }

    /**
     * Covoiturages où l'utilisateur est passager
     */
    public function getQueryBuilderByPassager(User $user)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.passagers', 'p')
            ->where('p = :user')
            ->setParameter('user', $user)
            ->orderBy('c.datedepart', 'DESC');
    }

    /**
     * Historique des covoiturages terminés pour un utilisateur
     */
    public function getQueryBuilderHistoriqueByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.passagers', 'p')
            ->where('c.user = :user OR p = :user')
            ->andWhere('c.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', 'Terminé')
            ->orderBy('c.datedepart', 'DESC');
    }

    public function search(?string $depart, ?string $arrivee, ?\DateTime $date): array
    {
        $qb = $this->searchQueryBuilder($depart ?? '', $arrivee ?? '', $date ?? new \DateTime());

        if ($date) {
            $start = (clone $date)->setTime(0,0);
            $end = (clone $date)->setTime(23,59,59);
            $qb->andWhere('c.datedepart BETWEEN :start AND :end')
               ->setParameter('start', $start)
               ->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();
    }
}
