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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'Livro')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Book
{
    # Timestamp e Softdelete criado para implementar a rastreabilidade desse modo podemos ver quando a informação foi criada, editada e deletada
    use TimestampableTrait, SoftDeleteableTrait;

    # TODO rever nome da coluna (CodL ou CodI), não ficou claro na imagem.
    # Usando CodL para seguir o padrão das outras tabelas (CodAu, CodAs).
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CodL')]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    #[ORM\Column(name: 'Titulo', length: 40)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    #[ORM\Column(name: 'Editora', length: 40)]
    private ?string $publisher = null;

    #[Assert\NotNull]
    #[Assert\Positive]
    #[ORM\Column(name: 'Edicao')]
    private ?int $edition = null;

    #[Assert\NotNull]
    # usando maxPropertyPath para limitar o ano da publicação com o ano atual mais um
    #[Assert\Range(min:1000, maxPropertyPath: 'maxYear')]
    # Usando smallint no ano de publicação para economizar espaço, melhora o índice de pesquisa e garante que o valor esperado seja um ano válido.
    #[ORM\Column(name: 'AnoPublicacao', type: Types::SMALLINT)]
    private ?int $publicationYear = null;

    #[Assert\NotNull()]
    #[Assert\PositiveOrZero()]
    #[ORM\Column(name: 'Valor', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    /**
     * @var Collection<int, Author>
     */
    #[Assert\Count(min: 1, minMessage: 'Selecione pelo menos um autor')]
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'Livro_Autor')]
    #[ORM\JoinColumn(name: 'Livro_CodL', referencedColumnName: 'CodL')]
    #[ORM\InverseJoinColumn(name: 'Autor_CodAu', referencedColumnName: 'CodAu')]
    private Collection $authors;

    /**
     * @var Collection<int, Subject>
     */
    #[Assert\Count(min: 1, minMessage: 'Selecione pelo menos um assunto/tema')]
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
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
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

    # Criando essa função, para pegar o ano atual mais 1
    public function getMaxYear(): int
    {
        return (int) date('Y') + 1;
    }
}
