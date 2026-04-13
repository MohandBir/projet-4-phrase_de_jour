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
        return $this->render('admin/sentence/index.html.twig', [
            'sentences' => $sentenceRepo->findBy(
                [],
                ['createdAt' => 'DESC'])
        ]);
    }

    #[Route('/admin/save/{id?null}', name: 'app_admin_save')]
    // #[IsGranted('ROLE_ADMIN')]
    public function save(?Sentence $sentence, Request $request, EntityManagerInterface $em): Response
    {
        if ( !$this->IsGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }

        $isUpdated = (bool)$sentence;

        // creation de formulaire add
        $form = $this->createForm(SentenceType::class, $sentence);
        $form->handleRequest($request);
        $data = $form->getData();

        // gestion de soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $sentence->setContent($data->getContent())
            ->setCreatedAt(new DateTimeImmutable())
            ->setLikes(0)
            ->setCategory($data->getCategory())
            ;
            $em->persist($sentence);
            $em->flush();

            $action = ($isUpdated) ? substr($sentence->getContent(), 0, 20) . '..." est modifié' : substr($sentence->getContent(), 0, 10) . '...\"] ajoutée' ;
            $this->addFlash('success', "La phrase \"$action avec succès !");
            
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/sentence/save.html.twig', [
            'formView' => $form->createView(),
            'isUpdated' => $isUpdated,
        ]);
    }
}
