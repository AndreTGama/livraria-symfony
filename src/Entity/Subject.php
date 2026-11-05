<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'Assunto')]
#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Subject
{
    // Timestamp e Softdelete criado para implementar a rastreabilidadem desse modo podemos ver quando a informação foi criada, editada e deletada
    use TimestampableTrait, SoftDeleteableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CodAs')]
    private ?int $id = null;

    #[ORM\Column(name: 'Descricao', length: 20)]
    private ?string $description = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'subjects')]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addSubject($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            $book->removeSubject($this);
        }

        return $this;
    }
}
