<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261106001102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a VIEW do relatório de livros agrupados por autor';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW vw_livros_por_autor AS
            SELECT
                a.CodAu AS autor_codigo,
                a.Nome AS autor_nome,
                l.CodL AS livro_codigo,
                l.Titulo AS titulo,
                l.Editora AS editora,
                l.Edicao AS edicao,
                l.AnoPublicacao AS ano_publicacao,
                l.Valor AS valor,
            GROUP_CONCAT(s.Descricao ORDER BY s.Descricao SEPARATOR ', ') AS assuntos
            FROM Autor a
            JOIN Livro_Autor la ON la.Autor_CodAu = a.CodAu
            JOIN Livro l ON l.CodL = la.Livro_CodL AND l.DeletedAt IS NULL
            LEFT JOIN Livro_Assunto ls ON ls.Livro_CodL = l.CodL
            LEFT JOIN Assunto s ON s.CodAs = ls.Assunto_CodAs AND s.DeletedAt IS NULL
            WHERE a.DeletedAt IS NULL
            GROUP BY a.CodAu, a.Nome, l.CodL, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW vw_livros_por_autor');
    }
}
