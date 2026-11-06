<?php

namespace App\DataTables;

use App\Entity\Book;
use App\Repository\BookRepository;
use Pentiminax\UX\DataTables\Column\DateColumn;
use Pentiminax\UX\DataTables\Column\MoneyColumn;
use Pentiminax\UX\DataTables\Column\NumberColumn;
use Pentiminax\UX\DataTables\Column\TextColumn;
use Pentiminax\UX\DataTables\Enum\Icon;
use Pentiminax\UX\DataTables\Model\AbstractDataTable;
use Pentiminax\UX\DataTables\Model\Action;
use Pentiminax\UX\DataTables\Model\Actions;
use Pentiminax\UX\DataTables\Model\DataTable;

final class BooksDataTable extends AbstractDataTable
{
    public function __construct(private BookRepository $bookRepository) {}

    public function configureColumns(): iterable
    {
        yield NumberColumn::new('id', 'ID');
        yield TextColumn::new('title', 'Título');
        yield TextColumn::new('publisher', 'Publicado');
        yield NumberColumn::new('edition', 'Edição');
        yield DateColumn::new('publicationYear', 'Ano da publicação')->setFormat('d/m/Y');
        yield MoneyColumn::new('price', 'Preço')
            ->currency('BRL')
            ->storedAsCents(true)
            ->decimals(2);
        yield TextColumn::new('authors', 'Autores');
        yield TextColumn::new('subjects', 'Assuntos');
        yield DateColumn::new('createdAt', 'Criado')->setFormat('d/m/Y h:i');
        yield DateColumn::new('updatedAt', 'Última atualização')->setFormat('d/m/Y h:i');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setColumnLabel('Ações')
            ->add(
                Action::edit('Editar')
                    ->setClassName('button-custom')
                    ->icon(Icon::Pencil)
                    ->linkToRoute('app_book_edit', fn(Book $book): array => ['id' => $book->getId()])
            )
            ->add(
                Action::detail('Visualizar')
                    ->setClassName('btn btn-sm btn-info')
                    ->icon(Icon::Eye)
                    ->linkToRoute('app_book_show', fn(Book $book): array => ['id' => $book->getId()])
            );
    }

    public function configureDataTable(DataTable $table): DataTable
    {
        $books = $this->bookRepository->findAll();
        return $table
            ->data($books)
            ->pageLength(10)
            ->searching(true)
            ->ordering(handler: true, indicators: true);
    }

    protected function mapRow(mixed $row): array
    {
        /** @var Book $row */
        return [
            'id'                => $row->getId(),
            'title'             => $row->getTitle(),
            'publisher'         => $row->getPublisher(),
            'edition'           => $row->getEdition(),
            'publicationYear'   => $row->getPublicationYear(),
            'price'             => $row->getPrice(),
            'authors'           => implode(', ', $row->getAuthors()->map(fn($author) => $author->getName())->toArray()),
            'subjects'          => implode(', ', $row->getSubjects()->map(fn($subject) => $subject->getDescription())->toArray()),
            'createdAt'         => $row->getCreatedAt(),
            'updatedAt'         => $row->getUpdatedAt(),
        ];
    }
}
