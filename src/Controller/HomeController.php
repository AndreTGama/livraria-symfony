<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(private SubjectRepository $subjectRepository)
    {
    }
    
    
    #[Route('/', name: 'app_home')]
    public function index(#[MapQueryParameter] ?int $subject = null): Response
    {
        return $this->render('home/index.html.twig', [
            'subjects'      => $this->subjectRepository->findAllWithBookCount(),
            'subjectActive' => $subject 
        ]);
    }
}
