<?php

namespace App\Controller;

use App\Entity\Sentence;
use App\Entity\User;
use App\Form\SentenceType;
use App\Repository\SentenceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            'sentences' => $sentenceRepo->findBy(
                [],
                ['createdAt' => 'DESC'])
        ]);
    }

    #[Route('/admin/add', name: 'app_admin_add')]
    // #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        if ( !$this->IsGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }
        // creation de formulaire add
        $form = $this->createForm(SentenceType::class);
        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $sentence = new Sentence;
            $sentence->setContent($data->getContent())
            ->setCreatedAt(new DateTimeImmutable())
            ->setLikes(0)
            ->setCategory($data->getCategory())
            ;
            $em->persist($sentence);
            $em->flush();

            $this->addFlash('success', 'La phrase est ajoutée avec succès !');
            
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/add.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
