<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_inscription_agent_formation', fields: ['agent', 'formation'])]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private Formation $formation;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Agent $agent;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Merci d\'indiquer votre motivation.')]
    #[Assert\Length(min: 20, max: 500)]
    private string $motivation = '';

    #[ORM\Column(enumType: InscriptionStatut::class)]
    private InscriptionStatut $statut = InscriptionStatut::EnAttente;

    #[ORM\Column]
    private \DateTimeImmutable $creeLe;

    public function __construct(Formation $formation, Agent $agent)
    {
        $this->formation = $formation;
        $this->agent = $agent;
        $this->creeLe = new \DateTimeImmutable();
        $formation->getInscriptions()->add($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormation(): Formation
    {
        return $this->formation;
    }

    public function getAgent(): Agent
    {
        return $this->agent;
    }

    public function getMotivation(): string
    {
        return $this->motivation;
    }

    public function setMotivation(string $motivation): static
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getStatut(): InscriptionStatut
    {
        return $this->statut;
    }

    public function setStatut(InscriptionStatut $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCreeLe(): \DateTimeImmutable
    {
        return $this->creeLe;
    }
}
