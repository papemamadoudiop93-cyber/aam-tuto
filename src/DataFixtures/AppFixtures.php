<?php

namespace App\DataFixtures;

use App\Entity\Agent;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\InscriptionStatut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** Données fictives : php bin/console doctrine:fixtures:load */
final class AppFixtures extends Fixture
{
    public const MOT_DE_PASSE = 'motdepasse';

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $agent = $this->agent('agent@exemple.fr', 'Camille Martin', []);
        $autre = $this->agent('agent2@exemple.fr', 'Sacha Bernard', []);
        $gestionnaire = $this->agent('gestion@exemple.fr', 'Dominique Petit', ['ROLE_GESTIONNAIRE']);
        foreach ([$agent, $autre, $gestionnaire] as $a) {
            $manager->persist($a);
        }

        $catalogue = [
            ['Symfony : les fondamentaux', 'Contrôleurs, routing, services et injection de dépendances.', 'Aix-en-Provence', '+10 days', 12],
            ['Doctrine ORM en pratique', 'Entités, relations, QueryBuilder, DQL et migrations.', 'Marseille', '+20 days', 10],
            ['Twig et Stimulus', 'Templates, héritage, macros et contrôleurs Stimulus.', 'Aix-en-Provence', '+30 days', 2],
            ['Système de Design de l\'État (DSFR)', 'Utiliser les composants DSFR dans une application.', 'À distance', '+45 days', 20],
            ['Sécurité Symfony et OpenID Connect', 'Firewalls, authenticators, voters et authentification fédérée.', 'Marseille', '+60 days', 8],
        ];

        $formations = [];
        foreach ($catalogue as [$titre, $description, $lieu, $quand, $places]) {
            $f = (new Formation())
                ->setTitre($titre)
                ->setDescription($description)
                ->setLieu($lieu)
                ->setDateDebut(new \DateTimeImmutable($quand.' 09:00'))
                ->setPlaces($places);
            $manager->persist($f);
            $formations[] = $f;
        }

        $demandes = [
            [$formations[0], $agent, InscriptionStatut::Validee],
            [$formations[1], $agent, InscriptionStatut::EnAttente],
            [$formations[2], $autre, InscriptionStatut::Validee],
            [$formations[2], $agent, InscriptionStatut::EnAttente],
            [$formations[3], $autre, InscriptionStatut::Refusee],
        ];
        foreach ($demandes as [$formation, $qui, $statut]) {
            $manager->persist(
                (new Inscription($formation, $qui))
                    ->setMotivation('Je souhaite monter en compétence sur ce sujet pour mes projets.')
                    ->setStatut($statut)
            );
        }

        $manager->flush();
    }

    /** @param list<string> $roles */
    private function agent(string $email, string $nom, array $roles): Agent
    {
        $a = (new Agent())->setEmail($email)->setNomComplet($nom)->setRoles($roles);

        return $a->setPassword($this->hasher->hashPassword($a, self::MOT_DE_PASSE));
    }
}
