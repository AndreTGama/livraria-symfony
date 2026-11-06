<?php

namespace App\DataTables;

use App\Entity\Book;
use App\Repository\BookRepository;
use Pentiminax\UX\DataTables\Column\DateColumn;
use Pentiminax\UX\DataTables\Column\NumberColumn;
use Pentiminax\UX\DataTables\Column\TextColumn;
use Pentiminax\UX\DataTables\Enum\Icon;
use Pentiminax\UX\DataTables\Model\AbstractDataTable;
use Pentiminax\UX\DataTables\Model\Action;
use Pentiminax\UX\DataTables\Model\Actions;
use Pentiminax\UX\DataTables\Model\DataTable;

final class AuthorsDataTable extends AbstractDataTable
{
    public function __construct(private BookRepository $bookRepository) {}

    public function configureColumns(): iterable
    {
        yield NumberColumn::new('id', 'ID');
        yield TextColumn::new('title', 'Título');
        yield TextColumn::new('publisher', 'Publicado');
        yield DateColumn::new('createdAt', 'Criado')->setFormat('d/m/Y h:i');
        yield DateColumn::new('updatedAt', 'Última atualização')->setFormat('d/m/Y h:i');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setColumnLabel('Ações')
            ->add(
                Action::edit('Editar')
                    ->setClassName('btn btn-sm btn-warning')
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
            'updatedAt'         => $row->getUpdatedAt(),
        ];
    }
}
