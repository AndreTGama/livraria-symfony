<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\AutorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'Autor')]
#[ORM\Entity(repositoryClass: AutorRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Autor
{
    // Timestamp e Softdelete criado para implementar a rastreabilidadem desse modo podemos ver quando a informação foi criada, editada e deletada
    use TimestampableTrait, SoftDeleteableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CodAu')]
    private ?int $id = null;

    #[ORM\Column(name: 'Nome', length: 40)]
    private ?string $nome = null;

    /**
     * @var Collection<int, Livro>
     */
    #[ORM\ManyToMany(targetEntity: Livro::class, mappedBy: 'autores')]
    private Collection $livros;

    public function __construct()
    {
        $this->livros = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * @return Collection<int, Livro>
     */
    public function getLivros(): Collection
    {
        return $this->livros;
    }

    public function addLivro(Livro $livro): static
    {
        if (!$this->livros->contains($livro)) {
            $this->livros->add($livro);
            $livro->addAutor($this);
        }

        return $this;
    }

    public function removeLivro(Livro $livro): static
    {
        if ($this->livros->removeElement($livro)) {
            $livro->removeAutor($this);
        }

        return $this;
    }
}
