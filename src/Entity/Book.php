<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'Livro')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Book
{
    // Timestamp e Softdelete criado para implementar a rastreabilidadem desse modo podemos ver quando a informação foi criada, editada e deletada
    use TimestampableTrait, SoftDeleteableTrait;

    // TODO rever nome da coluna (CodL ou CodI), não ficou claro na imagem.
    // Usando CodL para seguir o padrão das outras tabelas (CodAu, CodAs).
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CodL')]
    private ?int $id = null;

    #[ORM\Column(name: 'Titulo', length: 40)]
    private ?string $title = null;

    #[ORM\Column(name: 'Editora', length: 40)]
    private ?string $publisher = null;

    #[ORM\Column(name: 'Edicao')]
    private ?int $edition = null;

    // Usando smallint no ano de publicação para economizar espaço, melhora o índice de pesquisa e garante que o valor esperado seja um ano válido.
    #[ORM\Column(name: 'AnoPublicacao', type: Types::SMALLINT)]
    private ?int $publicationYear = null;

    #[ORM\Column(name: 'Valor', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    /**
     * @var Collection<int, Author>
     */
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'Livro_Autor')]
    #[ORM\JoinColumn(name: 'Livro_CodL', referencedColumnName: 'CodL')]
    #[ORM\InverseJoinColumn(name: 'Autor_CodAu', referencedColumnName: 'CodAu')]
    private Collection $authors;

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'Livro_Assunto')]
    #[ORM\JoinColumn(name: 'Livro_CodL', referencedColumnName: 'CodL')]
    #[ORM\InverseJoinColumn(name: 'Assunto_CodAs', referencedColumnName: 'CodAs')]
    private Collection $subjects;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->subjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getEdition(): ?int
    {
        return $this->edition;
    }

    public function setEdition(int $edition): static
    {
        $this->edition = $edition;

        return $this;
    }

    public function getPublicationYear(): ?int
    {
        return $this->publicationYear;
    }

    public function setPublicationYear(int $publicationYear): static
    {
        $this->publicationYear = $publicationYear;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAutores(): Collection
    {
        return $this->authors;
    }

    public function addAutor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAutor(Author $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getAssuntos(): Collection
    {
        return $this->subjects;
    }

    public function addAssunto(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        $this->subjects->removeElement($subject);

        return $this;
    }
}
