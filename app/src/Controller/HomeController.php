<?php

namespace App\Controller;

use App\Repository\SentenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SentenceRepository $sentenceRepo, EntityManagerInterface $manager): Response
    {
        $sentences = $sentenceRepo->findBy(
            [],    
            ['createdAt' => 'DESC']
        );
        
        return $this->render('home/index.html.twig', [
            'sentences' => $sentences
        ]);
    }
}
