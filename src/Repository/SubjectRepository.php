<?php

namespace App\Repository;

use App\Cache\InfraCache;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subject>
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private InfraCache $infraCache    
    )
    {
        parent::__construct($registry, Subject::class);
    }

    public function findAllWithBookCount(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id, s.description, COUNT(b.id) as total')
            ->leftJoin('s.books', 'b')
            ->groupBy('s.id, s.description')
            ->orderBy('s.description', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function cacheFindAll()
    {
        return $this->infraCache->cached('subjects', [], function () {
            return $this->findAll();
        });
    }
}
