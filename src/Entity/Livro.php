<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\LivroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: LivroRepository::class)]
#[ORM\Table(name: 'Livro')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Livro
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
    private ?string $titulo = null;

    #[ORM\Column(name: 'Editora', length: 40)]
    private ?string $editora = null;

    #[ORM\Column(name: 'Edicao')]
    private ?int $edicao = null;

    // Usando smallint no ano de publicação para economizar espaço, melhora o índice de pesquisa e garante que o valor esperado seja um ano válido.
    #[ORM\Column(name: 'AnoPublicacao', type: Types::SMALLINT)]
    private ?int $anoPublicacao = null;

    #[ORM\Column(name: 'Valor', type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $valor = null;

    /**
     * @var Collection<int, Autor>
     */
    #[ORM\ManyToMany(targetEntity: Autor::class, inversedBy: 'livros')]
    #[ORM\JoinTable(name: 'Livro_Autor')]
    #[ORM\JoinColumn(name: 'Livro_CodL', referencedColumnName: 'CodL')]
    #[ORM\InverseJoinColumn(name: 'Autor_CodAu', referencedColumnName: 'CodAu')]
    private Collection $autores;

    /**
     * @var Collection<int, Assunto>
     */
    #[ORM\ManyToMany(targetEntity: Assunto::class, inversedBy: 'livros')]
    #[ORM\JoinTable(name: 'Livro_Assunto')]
    #[ORM\JoinColumn(name: 'Livro_CodL', referencedColumnName: 'CodL')]
    #[ORM\InverseJoinColumn(name: 'Assunto_CodAs', referencedColumnName: 'CodAs')]
    private Collection $assuntos;

    public function __construct()
    {
        $this->autores = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getEditora(): ?string
    {
        return $this->editora;
    }

    public function setEditora(string $editora): static
    {
        $this->editora = $editora;

        return $this;
    }

    public function getEdicao(): ?int
    {
        return $this->edicao;
    }

    public function setEdicao(int $edicao): static
    {
        $this->edicao = $edicao;

        return $this;
    }

    public function getAnoPublicacao(): ?int
    {
        return $this->anoPublicacao;
    }

    public function setAnoPublicacao(int $anoPublicacao): static
    {
        $this->anoPublicacao = $anoPublicacao;

        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): static
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * @return Collection<int, Autor>
     */
    public function getAutores(): Collection
    {
        return $this->autores;
    }

    public function addAutor(Autor $autor): static
    {
        if (!$this->autores->contains($autor)) {
            $this->autores->add($autor);
        }

        return $this;
    }

    public function removeAutor(Autor $autor): static
    {
        $this->autores->removeElement($autor);

        return $this;
    }

    /**
     * @return Collection<int, Assunto>
     */
    public function getAssuntos(): Collection
    {
        return $this->assuntos;
    }

    public function addAssunto(Assunto $assunto): static
    {
        if (!$this->assuntos->contains($assunto)) {
            $this->assuntos->add($assunto);
        }

        return $this;
    }

    public function removeAssunto(Assunto $assunto): static
    {
        $this->assuntos->removeElement($assunto);

        return $this;
    }
}
