<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Contact;
use App\Form\CommentFormType;
use App\Service\SendMailService;
use App\Form\DistributorFormType;
use Symfony\Component\Mime\Email;
use App\Repository\CommentRepository;
use App\Repository\HeadersRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ProductRepository $productRepository,CommentRepository $commentRepository,  HeadersRepository $headersRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);
        $headers = $headersRepository->findAll();
        $comments = $commentRepository->findBy(['isValid' => true], ['createdAt' => 'DESC']);
        $averageRating = $commentRepository->averageRating();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'carousel' => true,  //Le caroussel ne s'affiche que sur la page d'accueil (voir base.twig)
            'top_products' => $products,
            'headers' => $headers,
            'comments' => $comments,
            'averageRating' => $averageRating,
            'products' => $products
        ]);
    }

    #[Route('/donner-avis', name: 'home_notice')]
    public function notice(Request $request, EntityManagerInterface $em, SendMailService $mail): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setFullname(ucwords($form->get('fullname')->getData()));
            $em->persist($comment);
            $em->flush();
            $mail->sendEmail(
                'no-reply@monsite.net',
                'Demande de contact',
                'contact@monsite.net',
                'Nouveau commentaire sur le site',
                'comment',
                []
            );
            $this->addFlash('success', 'Votre avis a bien été envoyé, il sera publié après validation !');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('home/notice.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
    #[Route('/devenir-distributeur', name: 'home_distributeur')]
    public function distributor(Request $request, EntityManagerInterface $em, SendMailService $mail): Response
    {
        $contact = new Contact();
        $form = $this->createForm(DistributorFormType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = ucwords($form->get('firstname')->getData());
            $lastname = mb_strtoupper($form->get('lastname')->getData());
            $contact->setFirstname($firstname)
                ->setLastname($lastname);
            
            $em->persist($contact);
            $em->flush();
            $mail->sendEmail(
               'no-reply@monsite.net',
                'Demande de contact',
                'contact@sfr.fr',
                'Demande de contact',
                'contact',
                ['contact' => $contact]
            );
            $this->addFlash('success', 'Votre demande de contact a été envoyée');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('home/distributor.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/liste-points-de-ventes', name: 'home_pdv')]
    public function pdv(): Response
    {
        return $this->render('home/pdv.html.twig');
    }
    #[Route('/test', name: 'home_test')]
    public function test(): Response
    {
        return $this->render('home/test.html.twig');
    }


    

}
