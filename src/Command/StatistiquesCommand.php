<?php

namespace App\Command;

use App\Repository\FormationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** Commande applicative : php bin/console app:formations:stats */
#[AsCommand(name: 'app:formations:stats', description: 'Affiche le nombre d\'inscriptions par formation et par statut')]
final class StatistiquesCommand extends Command
{
    public function __construct(private readonly FormationRepository $formations)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $lignes = array_map(
            static fn (array $l) => [$l['titre'], $l['statut']->libelle(), $l['total']],
            $this->formations->statistiques(),
        );

        if ([] === $lignes) {
            $io->warning('Aucune inscription. Chargez les données : php bin/console doctrine:fixtures:load');

            return Command::SUCCESS;
        }

        $io->table(['Formation', 'Statut', 'Total'], $lignes);

        return Command::SUCCESS;
    }
}
