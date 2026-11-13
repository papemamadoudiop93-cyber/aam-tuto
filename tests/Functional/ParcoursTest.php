<?php

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use App\Repository\AgentRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test fonctionnel : vraie application, vraie base (SQLite de test), requêtes HTTP simulées.
 */
final class ParcoursTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        // Base de test recréée à chaque test, puis remplie avec les fixtures.
        $em = $container->get(EntityManagerInterface::class);
        $schema = new SchemaTool($em);
        $schema->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $schema->createSchema($em->getMetadataFactory()->getAllMetadata());

        $loader = new Loader();
        $loader->addFixture(new AppFixtures($container->get(UserPasswordHasherInterface::class)));
        (new ORMExecutor($em, new ORMPurger()))->execute($loader->getFixtures());
    }

    private function connecter(string $email): void
    {
        $this->client->loginUser(static::getContainer()->get(AgentRepository::class)->findOneBy(['email' => $email]));
    }

    public function testLeCatalogueEstPublic(): void
    {
        $crawler = $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Catalogue des formations');
        self::assertCount(5, $crawler->filter('.fr-card'));
    }

    public function testMesInscriptionsExigeUneConnexion(): void
    {
        $this->client->request('GET', '/mes-inscriptions');

        self::assertResponseRedirects('/connexion');
    }

    public function testUnAgentNAccedePasALaGestion(): void
    {
        $this->connecter('agent@exemple.fr');
        $this->client->request('GET', '/gestion');

        self::assertResponseStatusCodeSame(403);
    }

    public function testUnAgentPeutDemanderUneInscription(): void
    {
        $this->connecter('agent2@exemple.fr');
        $crawler = $this->client->request('GET', '/');
        $lien = $crawler->filter('.fr-card__title a')->link();
        $crawler = $this->client->click($lien);

        $this->client->submitForm('Envoyer ma demande', [
            'inscription[motivation]' => 'Cette formation correspond à mon nouveau poste.',
        ]);

        self::assertResponseRedirects('/mes-inscriptions');
        $this->client->followRedirect();
        self::assertSelectorTextContains('.fr-alert--success', 'Votre demande a été enregistrée');
    }

    public function testUneMotivationTropCourteEstRefusee(): void
    {
        $this->connecter('agent2@exemple.fr');
        $this->client->request('GET', '/');
        $this->client->clickLink('Symfony : les fondamentaux');

        $this->client->submitForm('Envoyer ma demande', ['inscription[motivation]' => 'court']);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorExists('.fr-error-text');
    }

    public function testLeGestionnaireVoitLesDemandesEnAttente(): void
    {
        $this->connecter('gestion@exemple.fr');
        $crawler = $this->client->request('GET', '/gestion');

        self::assertResponseIsSuccessful();
        self::assertCount(2, $crawler->filter('tbody tr'));
    }
}
