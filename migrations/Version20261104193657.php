<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261104193657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Assunto (CodAs INT AUTO_INCREMENT NOT NULL, Descricao VARCHAR(20) NOT NULL, PRIMARY KEY (CodAs)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Autor (CodAu INT AUTO_INCREMENT NOT NULL, Nome VARCHAR(40) NOT NULL, PRIMARY KEY (CodAu)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Livro (CodL INT AUTO_INCREMENT NOT NULL, Titulo VARCHAR(40) NOT NULL, Editora VARCHAR(40) NOT NULL, Edicao INT NOT NULL, AnoPublicacao SMALLINT NOT NULL, Valor NUMERIC(10, 2) NOT NULL, PRIMARY KEY (CodL)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Livro_Autor (Livro_CodL INT NOT NULL, Autor_CodAu INT NOT NULL, INDEX Livro_Autor_FKIndex1 (Livro_CodL), INDEX Livro_Autor_FKIndex2 (Autor_CodAu), PRIMARY KEY (Livro_CodL, Autor_CodAu)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Livro_Assunto (Livro_CodL INT NOT NULL, Assunto_CodAs INT NOT NULL, INDEX Livro_Assunto_FKIndex1 (Livro_CodL), INDEX Livro_Assunto_FKIndex2 (Assunto_CodAs), PRIMARY KEY (Livro_CodL, Assunto_CodAs)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE Livro_Autor ADD CONSTRAINT FK_412939417134DCF1 FOREIGN KEY (Livro_CodL) REFERENCES Livro (CodL)');
        $this->addSql('ALTER TABLE Livro_Autor ADD CONSTRAINT FK_41293941B44F3F36 FOREIGN KEY (Autor_CodAu) REFERENCES Autor (CodAu)');
        $this->addSql('ALTER TABLE Livro_Assunto ADD CONSTRAINT FK_2F01B7437134DCF1 FOREIGN KEY (Livro_CodL) REFERENCES Livro (CodL)');
        $this->addSql('ALTER TABLE Livro_Assunto ADD CONSTRAINT FK_2F01B74364209C06 FOREIGN KEY (Assunto_CodAs) REFERENCES Assunto (CodAs)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Livro_Autor DROP FOREIGN KEY FK_412939417134DCF1');
        $this->addSql('ALTER TABLE Livro_Autor DROP FOREIGN KEY FK_41293941B44F3F36');
        $this->addSql('ALTER TABLE Livro_Assunto DROP FOREIGN KEY FK_2F01B7437134DCF1');
        $this->addSql('ALTER TABLE Livro_Assunto DROP FOREIGN KEY FK_2F01B74364209C06');
        $this->addSql('DROP TABLE Assunto');
        $this->addSql('DROP TABLE Autor');
        $this->addSql('DROP TABLE Livro');
        $this->addSql('DROP TABLE Livro_Autor');
        $this->addSql('DROP TABLE Livro_Assunto');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
