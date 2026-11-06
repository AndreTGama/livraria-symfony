<?php 

namespace App\Service;

use App\Entity\Author;
use App\Entity\Subject;
use App\Exception\AssociatedSubjectException;
use Doctrine\ORM\EntityManagerInterface;

final class SubjectService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function delete(Subject $subject): void
    {
        if($subject->hasBooks()) {
            throw new AssociatedSubjectException('Assunto tem livros vinculados, e não pode ser excluído', 409);
        }

        $this->entityManager->remove($subject);
        $this->entityManager->flush();
    }

}