<?php

namespace App\Controller;

use App\DataTables\SubjectsDataTable;
use App\Entity\Subject;
use App\Exception\AssociatedSubjectException;
use App\Form\SubjectType;
use App\Service\SubjectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assuntos')]
final class SubjectController extends AbstractController
{
    public function __construct(private SubjectService $subjectService) {}


    #[Route(name: 'app_subject_index', methods: ['GET'])]
    public function index(SubjectsDataTable $table): Response
    {
        return $this->render('subject/index.html.twig', [
            'table' => $table,
        ]);
    }

    #[Route('/new', name: 'app_subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subject/new.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subject_show', methods: ['GET'])]
    public function show(Subject $subject): Response
    {
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subject_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Assunto atualizado com sucesso');

            return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subject/edit.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subject_delete', methods: ['POST'])]
    public function delete(Request $request, Subject $subject): Response
    {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);
        try {
            $this->subjectService->delete($subject);
            $this->addFlash('success', 'Assunto excluído com sucesso');

            return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        } catch (AssociatedSubjectException $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute(
                'app_subject_edit',
                ['id' => $subject->getId()],
                Response::HTTP_SEE_OTHER
            );
        }
    }
}
