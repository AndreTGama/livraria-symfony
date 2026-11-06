<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    public function __construct(private ReportRepository $reportRepository) {}

    #[Route('/relatorios', name: 'app_report_index')]
    public function index(): Response
    {
        $reportRepository = $this->reportRepository->groupBooksByAuthor();

        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository
        ]);
    }

    #[Route('/relatorios/pdf', name: 'app_report_pdf')]
    public function reportPdf(): Response
    {
        $html = $this->renderView('report/pdf.html.twig', [
            'reports' => $this->reportRepository->groupBooksByAuthor(),
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
}
