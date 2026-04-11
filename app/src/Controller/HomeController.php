<?php

namespace App\Controller;

use App\Entity\Sentence;
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

    #[Route('/show/{id}', name: 'app_home_show')]
    public function show(Sentence $sentence, EntityManagerInterface $manager): Response
    {

        return $this->render('home/show.html.twig', [
            'sentence' => $sentence
        ]);
    }
}
