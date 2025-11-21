<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Voiture;
use App\Entity\Avis;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CovoiturageRepository")
 */
#[ORM\Entity(repositoryClass: 'App\Repository\CovoiturageRepository')]
class Covoiturage
{
    public const STATUT_DISPONIBLE = 'Disponible';
    public const STATUT_EN_COURS = 'En cours';
    public const STATUT_TERMINE = 'Terminé';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $datedepart = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heuredepart = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $lieudepart = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $datearrive = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heurearrive = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $lieuarrivee = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $statut = self::STATUT_DISPONIBLE;

    #[ORM\Column(type: 'integer')]
    private ?int $nbplace = null;

    #[ORM\Column(type: 'float')]
    private float $prixpersonne;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $AccepteFumeur = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $AccepteAnimaux = null;

    #[ORM\OneToMany(mappedBy: 'covoiturage', targetEntity: Avis::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $avis;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'covoiturages')]
    private Collection $passagers;

    #[ORM\ManyToOne(inversedBy: 'covoiturages', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'voiture_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Voiture $voiture = null;

    public function __construct()
    {
        $this->passagers = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->actif = true;
        $this->voiture = null;
    }

    // -------------------- GETTERS / SETTERS --------------------

    public function getId(): ?int { return $this->id; }
    public function getDatedepart(): ?\DateTimeInterface { return $this->datedepart; }
    public function setDatedepart(\DateTimeInterface $datedepart): self { $this->datedepart = $datedepart; return $this; }
    public function getHeuredepart(): ?\DateTimeInterface { return $this->heuredepart; }
    public function setHeuredepart(\DateTimeInterface $heuredepart): self { $this->heuredepart = $heuredepart; return $this; }
    public function getLieudepart(): ?string { return $this->lieudepart; }
    public function setLieudepart(string $lieudepart): self { $this->lieudepart = $lieudepart; return $this; }
    public function getDatearrive(): ?\DateTimeInterface { return $this->datearrive; }
    public function setDatearrive(\DateTimeInterface $datearrive): self { $this->datearrive = $datearrive; return $this; }
    public function getHeurearrive(): ?\DateTimeInterface { return $this->heurearrive; }
    public function setHeurearrive(\DateTimeInterface $heurearrive): self { $this->heurearrive = $heurearrive; return $this; }
    public function getLieuarrivee(): ?string { return $this->lieuarrivee; }
    public function setLieuarrivee(string $lieuarrivee): self { $this->lieuarrivee = $lieuarrivee; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function getNbplace(): ?int { return $this->nbplace; }
    public function setNbplace(int $nbplace): self { $this->nbplace = $nbplace; return $this; }
    public function getPrixpersonne(): ?float { return $this->prixpersonne; }
    public function setPrixpersonne(float $prixpersonne): self { $this->prixpersonne = $prixpersonne; return $this; }
    public function getAccepteFumeur(): ?bool { return $this->AccepteFumeur; }
    public function setAccepteFumeur(?bool $AccepteFumeur): self { $this->AccepteFumeur = $AccepteFumeur; return $this; }
    public function getAccepteAnimaux(): ?bool { return $this->AccepteAnimaux; }
    public function setAccepteAnimaux(?bool $AccepteAnimaux): self { $this->AccepteAnimaux = $AccepteAnimaux; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getPassagers(): Collection { return $this->passagers; }
    public function addPassager(User $passager): self { 
        if (!$this->passagers->contains($passager)) { 
            $this->passagers->add($passager); 
            $passager->addCovoiturage($this); 
        } 
        return $this; 
    }
    public function removePassager(User $passager): self { 
        if ($this->passagers->removeElement($passager)) { 
            $passager->removeCovoiturage($this); 
        } 
        return $this; 
    }
    public function getVoiture(): ?Voiture { return $this->voiture; }
    public function setVoiture(?Voiture $voiture): self { $this->voiture = $voiture; return $this; }
    public function getAvis(): Collection { return $this->avis; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    // -------------------- MÉTHODES DE STATUT --------------------
    public function demarrer(): self
    {
        $this->statut = self::STATUT_EN_COURS;
        $this->actif = true;
        return $this;
    }

    public function arreter(): self
    {
        $this->statut = self::STATUT_TERMINE;
        $this->actif = false;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            'Covoiturage %d: %s → %s le %s à %s',
            $this->id,
            $this->lieudepart,
            $this->lieuarrivee,
            $this->datedepart ? $this->datedepart->format('Y-m-d') : 'N/A',
            $this->heuredepart ? $this->heuredepart->format('H:i') : 'N/A'
        );
    }
}
