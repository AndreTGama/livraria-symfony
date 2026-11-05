<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        BookRepository $bookRepository,
        SubjectRepository $subjectRepository,
        AuthorRepository $authorRepository,
    ): Response {
        $query = $request->query->all();
        $subjectIds = $this->normalizeIds($query['subjects'] ?? []);
        $authorIds = $this->normalizeIds($query['authors'] ?? []);
        $search = is_string($query['q'] ?? null) ? trim($query['q']) : '';
        $search = $search !== '' ? $search : null;
        $page = filter_var($query['page'] ?? 1, FILTER_VALIDATE_INT);
        $page = $page !== false && $page > 0 ? $page : 1;
        $catalog = $bookRepository->findForCatalog($subjectIds, $authorIds, $search, $page);

        return $this->render('home/index.html.twig', [
            'books' => $catalog['books'],
            'totalBooks' => $catalog['total'],
            'page' => $catalog['page'],
            'totalPages' => $catalog['totalPages'],
            'subjects' => $subjectRepository->findAllWithBookCount(),
            'authors' => $authorRepository->findBy([], ['name' => 'ASC']),
            'selectedSubjectIds' => $subjectIds,
            'selectedAuthorIds' => $authorIds,
            'search' => $search ?? '',
        ]);
    }

    /**
     * @return int[]
     */
    private function normalizeIds(mixed $values): array
    {
        if (!is_array($values)) {
            return [];
        }

        $ids = [];
        foreach ($values as $value) {
            $id = filter_var($value, FILTER_VALIDATE_INT);
            if ($id !== false && $id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }
}
