<?php

namespace App\EventSubscriber;

use App\Event\InscriptionValideeEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Écoute InscriptionValideeEvent. Enregistré automatiquement grâce à "autoconfigure".
 * Vérifier : php bin/console debug:event-dispatcher "App\Event\InscriptionValideeEvent"
 */
final class JournalInscriptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [InscriptionValideeEvent::class => 'onInscriptionValidee'];
    }

    public function onInscriptionValidee(InscriptionValideeEvent $event): void
    {
        $i = $event->inscription;
        $this->logger->info('Inscription validée', [
            'agent' => $i->getAgent()->getEmail(),
            'formation' => $i->getFormation()->getTitre(),
        ]);
    }
}
