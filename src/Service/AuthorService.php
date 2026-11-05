<?php 

namespace App\Service;

use App\Entity\Author;
use App\Exception\AssociatedAuthorException;
use Doctrine\ORM\EntityManagerInterface;

final class AuthorService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function delete(Author $author): void
    {
        if($author->hasBooks()) {
            throw new AssociatedAuthorException('Autor tem livros vinculados, e não pode ser excluído', 409);
        }

        $this->entityManager->remove($author);
        $this->entityManager->flush();
    }

}