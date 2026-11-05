<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\UnicodeString;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param int[] $subjectIds
     * @param int[] $authorIds
     *
     * @return array{books: list<Book>, total: int, page: int, totalPages: int}
     */
    public function findForCatalog(array $subjectIds, array $authorIds, ?string $search, int $page, int $limit = 1): array
    {
        $queryBuilder = $this->createQueryBuilder('book')
            ->leftJoin('book.subjects', 'subject')
            ->addSelect('subject')
            ->leftJoin('book.authors', 'author')
            ->addSelect('author')
            ->orderBy('book.title', 'ASC')
            ->addOrderBy('book.id', 'ASC');

        if ($subjectIds !== []) {
            $queryBuilder
                ->andWhere('subject.id IN (:subjectIds)')
                ->setParameter('subjectIds', $subjectIds);
        }

        if ($authorIds !== []) {
            $queryBuilder
                ->andWhere('author.id IN (:authorIds)')
                ->setParameter('authorIds', $authorIds);
        }

        if ($search !== null && $search !== '') {
            $queryBuilder
                ->andWhere('LOWER(book.title) LIKE :search OR LOWER(book.publisher) LIKE :search')
                ->setParameter('search', '%'.(new UnicodeString($search))->lower()->toString().'%');
        }

        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query, true);
        $total = count($paginator);
        $totalPages = max(1, (int) ceil($total / $limit));
        $page = min(max(1, $page), $totalPages);

        $query
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return [
            'books' => iterator_to_array($paginator, false),
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
        ];
    }
}
