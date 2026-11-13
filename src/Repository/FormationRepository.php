<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Formation> */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * Formations à venir, avec leurs inscriptions chargées en une seule requête
     * (évite le problème N+1 quand la liste affiche les places restantes).
     *
     * @return list<Formation>
     */
    public function findAVenir(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.inscriptions', 'i')
            ->addSelect('i')
            ->andWhere('f.dateDebut >= :maintenant')
            ->setParameter('maintenant', new \DateTimeImmutable('today'))
            ->orderBy('f.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Exemple de DQL "brut" : nombre d'inscriptions par statut et par formation.
     *
     * @return list<array{titre: string, statut: \App\Entity\InscriptionStatut, total: int}>
     */
    public function statistiques(): array
    {
        return $this->getEntityManager()->createQuery(
            'SELECT f.titre AS titre, i.statut AS statut, COUNT(i.id) AS total
             FROM App\Entity\Formation f
             JOIN f.inscriptions i
             GROUP BY f.id, f.titre, i.statut
             ORDER BY f.titre ASC'
        )->getResult();
    }
}
