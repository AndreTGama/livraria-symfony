<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261108195436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona índices FULLTEXT para livros, autores e assuntos';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE Livro ADD FULLTEXT INDEX ft_livro_titulo_editora (Titulo, Editora)');
        $this->addSql('ALTER TABLE Autor ADD FULLTEXT INDEX ft_autor_nome (Nome)');
        $this->addSql('ALTER TABLE Assunto ADD FULLTEXT INDEX ft_assunto_descricao (Descricao)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX ft_assunto_descricao ON Assunto');
        $this->addSql('DROP INDEX ft_autor_nome ON Autor');
        $this->addSql('DROP INDEX ft_livro_titulo_editora ON Livro');
    }
}
