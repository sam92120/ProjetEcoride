<?php

namespace App\Entity;

use App\Repository\AccueilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccueilRepository::class)]
class Accueil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Depart = null;

    #[ORM\Column(length: 255)]
    private ?string $Arrivee = null;

    #[ORM\Column]
    private ?\DateTime $DateArrive = null;

    #[ORM\Column]
    private ?\DateTime $dateDepart = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepart(): ?string
    {
        return $this->Depart;
    }

    public function setDepart(string $Depart): static
    {
        $this->Depart = $Depart;

        return $this;
    }

    public function getArrivee(): ?string
    {
        return $this->Arrivee;
    }

    public function setArrivee(string $Arrivee): static
    {
        $this->Arrivee = $Arrivee;

        return $this;
    }

    public function getDateArrive(): ?\DateTime
    {
        return $this->DateArrive;
    }

    public function setDateArrive(\DateTime $DateArrive): static
    {
        $this->DateArrive = $DateArrive;

        return $this;
    }

    public function getDateDepart(): ?\DateTime
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTime $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }
}
