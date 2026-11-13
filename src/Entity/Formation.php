<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 160)]
    private string $titre = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $description = '';

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    private string $lieu = '';

    #[ORM\Column]
    private \DateTimeImmutable $dateDebut;

    #[ORM\Column]
    #[Assert\Positive]
    private int $places = 10;

    /** @var Collection<int, Inscription> */
    #[ORM\OneToMany(targetEntity: Inscription::class, mappedBy: 'formation', orphanRemoval: true)]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->dateDebut = new \DateTimeImmutable('+1 month');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDateDebut(): \DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getPlaces(): int
    {
        return $this->places;
    }

    public function setPlaces(int $places): static
    {
        $this->places = $places;

        return $this;
    }

    /** @return Collection<int, Inscription> */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function getPlacesRestantes(): int
    {
        $validees = $this->inscriptions->filter(
            static fn (Inscription $i) => InscriptionStatut::Validee === $i->getStatut()
        )->count();

        return max(0, $this->places - $validees);
    }
}
