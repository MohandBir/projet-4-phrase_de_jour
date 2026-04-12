<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SentenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_index')]
    // #[IsGranted('ROLE_ADMIN')]
    public function index(SentenceRepository $sentenceRepo): Response
    {
        if ( !$this->IsGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('admin/index.html.twig', [
            'sentences' => $sentenceRepo->findAll()
        ]);
    }
}
