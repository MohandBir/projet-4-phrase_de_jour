<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Sentence;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\SentenceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SentenceRepository $sentenceRepo,): Response
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
    public function show(Sentence $sentence, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

            if ($this->getUser() && $form->isSubmitted() && $form->isValid()) {
                $comment = new Comment;
                dump($form->getData());
                $comment->setContent($form->getData()->getContent())
                ->setCreatedAt(new DateTimeImmutable())
                ->setSentence($sentence)
                ->setUser($this->getUser())
                ;
                $em->persist($comment);
                $em->flush();
                return $this->redirectToRoute('app_home_show', [
                    'id' => $sentence->getId(),
                ]);           
            }

       
        return $this->render('home/show.html.twig', [
            'sentence' => $sentence,
            'formView' => $form->createView(),
        ]);
    }
 
    #[Route('/show/like/{id}', name: 'app_home_like')]
    public function like($id, Sentence $sentence, EntityManagerInterface $manager): Response
    {
        $sentence->setLikes($sentence->getLikes() + 1);
        $manager->flush();

        return $this->redirectToRoute('app_home_show', [
            'id' => $id,
            ]);
        
    }

}
