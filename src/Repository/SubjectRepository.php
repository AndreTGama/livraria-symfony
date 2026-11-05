<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subject>
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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
}
