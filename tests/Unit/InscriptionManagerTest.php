<?php

namespace App\Tests\Unit;

use App\Entity\Agent;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\InscriptionStatut;
use App\Event\InscriptionValideeEvent;
use App\Service\InscriptionImpossibleException;
use App\Service\InscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Test unitaire : pas de base de données ni de noyau Symfony.
 * Les dépendances du service sont remplacées par des doublures (mocks/stubs).
 */
final class InscriptionManagerTest extends TestCase
{
    private function manager(?EventDispatcherInterface $dispatcher = null): InscriptionManager
    {
        return new InscriptionManager(
            $this->createStub(EntityManagerInterface::class),
            $dispatcher ?? $this->createStub(EventDispatcherInterface::class),
        );
    }

    private function formation(int $places = 2, string $quand = '+1 week'): Formation
    {
        return (new Formation())->setTitre('Test')->setPlaces($places)->setDateDebut(new \DateTimeImmutable($quand));
    }

    public function testInscrireCreeUneDemandeEnAttente(): void
    {
        $inscription = $this->manager()->inscrire($this->formation(), new Agent(), str_repeat('a', 25));

        self::assertSame(InscriptionStatut::EnAttente, $inscription->getStatut());
    }

    public function testOnNePeutPasSInscrireDeuxFois(): void
    {
        $formation = $this->formation();
        $agent = new Agent();
        $manager = $this->manager();
        $manager->inscrire($formation, $agent, 'première demande');

        $this->expectException(InscriptionImpossibleException::class);
        $manager->inscrire($formation, $agent, 'seconde demande');
    }

    public function testOnNePeutPasSInscrireAUneFormationCommencee(): void
    {
        $this->expectException(InscriptionImpossibleException::class);
        $this->manager()->inscrire($this->formation(quand: '-2 days'), new Agent(), 'trop tard');
    }

    public function testValiderDeclencheLEvenement(): void
    {
        $inscription = new Inscription($this->formation(), new Agent());

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(InscriptionValideeEvent::class))
            ->willReturnArgument(0);

        $this->manager($dispatcher)->valider($inscription);

        self::assertSame(InscriptionStatut::Validee, $inscription->getStatut());
    }

    public function testValiderEchoueQuandLaFormationEstComplete(): void
    {
        $formation = $this->formation(places: 1);
        (new Inscription($formation, new Agent()))->setStatut(InscriptionStatut::Validee);
        $demande = new Inscription($formation, new Agent());

        $this->expectExceptionMessage('plus de place');
        $this->manager()->valider($demande);
    }
}
