<?php 

namespace App\Repository;

use Doctrine\DBAL\Connection;

final class ReportRepository
{
    public function __construct(private Connection $connection)
    {
    }

    private function booksByAuthor(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM vw_livros_por_autor ORDER BY autor_nome, titulo'
        );
    }

    public function groupBooksByAuthor(): array
    {
        $list = [];
        $items = $this->booksByAuthor();

        foreach($items as $item){
            $list[$item['autor_codigo']][] = $item;
        }
        return $list;
    }
}