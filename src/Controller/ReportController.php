<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\ReportRepository;
use App\Repository\SubjectRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    public function __construct(private ReportRepository $reportRepository)
    {
    }

    #[Route('/relatorios', name: 'app_report_index')]
    public function index(
        Request $request,
        AuthorRepository $authorRepository,
        SubjectRepository $subjectRepository,
    ): Response {
        $filters = $this->getFilters($request);
        $report = $this->reportRepository->findForReport(
            $filters['subjectIds'],
            $filters['authorIds'],
            $filters['search'],
            $filters['page'],
        );

        return $this->render('report/index.html.twig', [
            ...$report,
            'authors' => $authorRepository->findBy([], ['name' => 'ASC']),
            'subjects' => $subjectRepository->findBy([], ['description' => 'ASC']),
            'selectedAuthorIds' => $filters['authorIds'],
            'selectedSubjectIds' => $filters['subjectIds'],
            'search' => $filters['search'] ?? '',
        ]);
    }

    #[Route('/relatorios/pdf', name: 'app_report_pdf')]
    public function reportPdf(Request $request): Response
    {
        ini_set('memory_limit', '1024M');

        $filters = $this->getFilters($request);
        $reports = $this->groupBooksByAuthor($this->reportRepository->findAllForExport(
            $filters['subjectIds'],
            $filters['authorIds'],
            $filters['search'],
        ));

        $html = $this->renderView('report/pdf.html.twig', [
            'reports' => $reports,
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="relatorio-livros-por-autor.pdf"',
        ]);
    }

    #[Route('/relatorios/planilha', name: 'app_report_csv')]
    public function reportCsv(Request $request): StreamedResponse
    {
        $filters = $this->getFilters($request);
        $rows = $this->reportRepository->findAllForExport(
            $filters['subjectIds'],
            $filters['authorIds'],
            $filters['search'],
        );

        return new StreamedResponse(static function () use ($rows): void {
            $output = fopen('php://output', 'wb');
            if ($output === false) {
                throw new \RuntimeException('Não foi possível gerar a planilha do relatório.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Autor', 'Título', 'Editora', 'Edição', 'Ano', 'Valor', 'Assuntos'], ';', '"', '', "\r\n");
            foreach ($rows as $row) {
                fputcsv($output, [
                    self::spreadsheetText((string) $row['autor_nome']),
                    self::spreadsheetText((string) $row['titulo']),
                    self::spreadsheetText((string) $row['editora']),
                    $row['edicao'],
                    $row['ano_publicacao'],
                    $row['valor'],
                    self::spreadsheetText((string) $row['assuntos']),
                ], ';', '"', '', "\r\n");
            }

            fclose($output);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="relatorio-livros-por-autor.csv"',
        ]);
    }

    /**
     * getFilters
     *
     * @param Request $request
     * @return array
     */
    private function getFilters(Request $request): array
    {
        $query = $request->query->all();
        $search = is_string($query['q'] ?? null) ? trim($query['q']) : '';
        $page = filter_var($query['page'] ?? 1, FILTER_VALIDATE_INT);

        return [
            'subjectIds' => $this->normalizeIds($query['subjects'] ?? []),
            'authorIds' => $this->normalizeIds($query['authors'] ?? []),
            'search' => $search !== '' ? $search : null,
            'page' => $page !== false && $page > 0 ? $page : 1,
        ];
    }

    /**
     * normalizeIds
     *
     * @param mixed $values
     * @return array
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

    /**
     * groupBooksByAuthor
     *
     * @param array $items
     * @return array
     */
    private function groupBooksByAuthor(array $items): array
    {
        $reports = [];
        foreach ($items as $item) {
            $reports[(int) $item['autor_codigo']][] = $item;
        }

        return $reports;
    }

    /**
     * spreadsheetText
     *
     * @param string $value
     * @return string
     */
    private static function spreadsheetText(string $value): string
    {
        if (preg_match('/^\s*[=+\-@\t\r]/u', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
