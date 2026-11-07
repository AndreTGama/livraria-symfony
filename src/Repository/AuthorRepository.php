<?php

namespace App\Repository;

use App\Cache\InfraCache;
use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private InfraCache $infraCache
    )
    {
        parent::__construct($registry, Author::class);
    }
    
    public function cacheFindAll()
    {
        return $this->infraCache->cached('authors', [], function () {
            return $this->findAll();
        });
    }
}
