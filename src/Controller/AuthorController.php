<?php

namespace App\Controller;

use App\DataTables\AuthorsDataTable;
use App\Entity\Author;
use App\Exception\AssociatedAuthorException;
use App\Form\AuthorType;
use App\Service\AuthorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/autores')]
final class AuthorController extends AbstractController
{
    public function __construct(private AuthorService $authorService) {}

    #[Route(name: 'app_author_index', methods: ['GET'])]
    public function index(AuthorsDataTable $table): Response
    {
        return $this->render('author/index.html.twig', [
            'table' => $table,
        ]);
    }

    #[Route('/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('danger', 'Não foi possível cadastrar o autor. Verifique os dados informados.');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_author_show', methods: ['GET'])]
    public function show(Author $author): Response
    {
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Autor atualizado com sucesso');
            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        try {
            $this->authorService->delete($author);
            $this->addFlash('success', 'Autor excluído com sucesso');

            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        } catch (AssociatedAuthorException $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute(
                'app_author_edit',
                ['id' => $author->getId()],
                Response::HTTP_SEE_OTHER
            );
        }
    }
}
