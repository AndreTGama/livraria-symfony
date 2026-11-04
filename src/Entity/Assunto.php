<?php

namespace App\Entity;

use App\Repository\AssuntoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'Assunto')]
#[ORM\Entity(repositoryClass: AssuntoRepository::class)]
class Assunto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codAs')]
    private ?int $id = null;

    #[ORM\Column(name: 'Descricao', length: 20)]
    private ?string $descricao = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }
}
