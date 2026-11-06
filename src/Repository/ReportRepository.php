<?php 

namespace App\Repository;

use Doctrine\DBAL\Connection;

final class ReportRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function booksByAuthor(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM vw_livros_por_autor ORDER BY autor_nome, titulo'
        );
    }
}