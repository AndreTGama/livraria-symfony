<?php

namespace App\DataTables;

use App\Entity\Author;
use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Pentiminax\UX\DataTables\Column\DateColumn;
use Pentiminax\UX\DataTables\Column\NumberColumn;
use Pentiminax\UX\DataTables\Column\TextColumn;
use Pentiminax\UX\DataTables\Enum\Icon;
use Pentiminax\UX\DataTables\Model\AbstractDataTable;
use Pentiminax\UX\DataTables\Model\Action;
use Pentiminax\UX\DataTables\Model\Actions;
use Pentiminax\UX\DataTables\Model\DataTable;

final class SubjectsDataTable extends AbstractDataTable
{
    public function __construct(private SubjectRepository $subjectRepository) {}

    public function configureColumns(): iterable
    {
        yield NumberColumn::new('id', 'ID');
        yield TextColumn::new('description', 'Descrição');
        yield NumberColumn::new('books', 'Livros publicados');
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
                    ->linkToRoute('app_subject_edit', fn(Subject $subject): array => ['id' => $subject->getId()])
            )
            ->add(
                Action::detail('Visualizar')
                    ->setClassName('btn btn-sm btn-info')
                    ->icon(Icon::Eye)
                    ->linkToRoute('app_subject_show', fn(Subject $subject): array => ['id' => $subject->getId()])
            );
    }

    public function configureDataTable(DataTable $table): DataTable
    {
        $subjects = $this->subjectRepository->findAll();
        return $table
            ->data($subjects)
            ->pageLength(10)
            ->searching(true)
            ->ordering(handler: true, indicators: true);
    }

    protected function mapRow(mixed $row): array
    {
        /** @var Subject $row */
        return [
            'id'            => $row->getId(),
            'description'   => $row->getDescription(),
            'books'         => $row->getBooks()->count(),
            'createdAt'     => $row->getCreatedAt(),
            'updatedAt'     => $row->getUpdatedAt(),
        ];
    }
}
