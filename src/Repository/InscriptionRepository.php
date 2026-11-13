<?php

namespace App\Repository;

use App\Entity\Agent;
use App\Entity\Inscription;
use App\Entity\InscriptionStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Inscription> */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    /** @return list<Inscription> */
    public function findPourAgent(Agent $agent): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.formation', 'f')
            ->addSelect('f')
            ->andWhere('i.agent = :agent')
            ->setParameter('agent', $agent)
            ->orderBy('f.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<Inscription> */
    public function findEnAttente(): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.formation', 'f')->addSelect('f')
            ->join('i.agent', 'a')->addSelect('a')
            ->andWhere('i.statut = :statut')
            ->setParameter('statut', InscriptionStatut::EnAttente)
            ->orderBy('i.creeLe', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
