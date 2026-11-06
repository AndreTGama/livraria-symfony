<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    public function __construct(private ReportRepository $reportRepository)
    {
    }

    #[Route('/relatorios', name: 'app_report_index')]
    public function index(): Response
    {
        $reportRepository = $this->reportRepository->groupBooksByAuthor();

        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository
        ]);
    }
}
