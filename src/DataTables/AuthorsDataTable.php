<?php

namespace App\DataTables;

use App\Entity\Author;
use App\Repository\AuthorRepository;
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
    public function __construct(private AuthorRepository $authorRepository) {}

    public function configureColumns(): iterable
    {
        yield NumberColumn::new('id', 'ID');
        yield TextColumn::new('name', 'Nome');
        yield NumberColumn::new('books', 'Livros com esse tema');
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
                    ->linkToRoute('app_author_edit', fn(Author $author): array => ['id' => $author->getId()])
            )
            ->add(
                Action::detail('Visualizar')
                    ->setClassName('btn btn-sm btn-info')
                    ->icon(Icon::Eye)
                    ->linkToRoute('app_author_show', fn(Author $author): array => ['id' => $author->getId()])
            );
    }

    public function configureDataTable(DataTable $table): DataTable
    {
        $author = $this->authorRepository->findAll();
        return $table
            ->data($author)
            ->pageLength(10)
            ->searching(true)
            ->ordering(handler: true, indicators: true);
    }

    protected function mapRow(mixed $row): array
    {
        /** @var Author $row */
        return [
            'id'        => $row->getId(),
            'name'      => $row->getName(),
            'books'     => $row->getBooks()->count(),
            'createdAt' => $row->getCreatedAt(),
            'updatedAt' => $row->getUpdatedAt(),
        ];
    }
}
