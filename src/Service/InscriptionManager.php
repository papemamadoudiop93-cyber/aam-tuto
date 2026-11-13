<?php

namespace App\Service;

use App\Entity\Agent;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\InscriptionStatut;
use App\Event\InscriptionValideeEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Règles métier des inscriptions. Les contrôleurs restent fins : ils appellent ce service.
 * Les dépendances arrivent par le constructeur (autowiring, cf. config/services.yaml).
 */
final class InscriptionManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function inscrire(Formation $formation, Agent $agent, string $motivation): Inscription
    {
        if ($formation->getDateDebut() < new \DateTimeImmutable('today')) {
            throw new InscriptionImpossibleException('Cette formation a déjà commencé.');
        }

        foreach ($formation->getInscriptions() as $existante) {
            if ($existante->getAgent() === $agent) {
                throw new InscriptionImpossibleException('Vous avez déjà une demande pour cette formation.');
            }
        }

        $inscription = (new Inscription($formation, $agent))->setMotivation($motivation);
        $this->em->persist($inscription);
        $this->em->flush();

        return $inscription;
    }

    public function valider(Inscription $inscription): void
    {
        if (InscriptionStatut::EnAttente !== $inscription->getStatut()) {
            throw new InscriptionImpossibleException('Seule une demande en attente peut être validée.');
        }

        if (0 === $inscription->getFormation()->getPlacesRestantes()) {
            throw new InscriptionImpossibleException('Il ne reste plus de place pour cette formation.');
        }

        $inscription->setStatut(InscriptionStatut::Validee);
        $this->em->flush();

        // Les effets de bord (journalisation, mail...) sont gérés par des abonnés à cet événement.
        $this->dispatcher->dispatch(new InscriptionValideeEvent($inscription));
    }

    public function refuser(Inscription $inscription): void
    {
        if (InscriptionStatut::EnAttente !== $inscription->getStatut()) {
            throw new InscriptionImpossibleException('Seule une demande en attente peut être refusée.');
        }

        $inscription->setStatut(InscriptionStatut::Refusee);
        $this->em->flush();
    }

    public function annuler(Inscription $inscription): void
    {
        $this->em->remove($inscription);
        $this->em->flush();
    }
}
