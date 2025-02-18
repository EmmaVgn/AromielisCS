<?php

namespace App\Controller;

use App\Repository\TagRepository;
use App\Repository\AdviseRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdviseController extends AbstractController
{
    #[Route('/conseils', name: 'advise')]
    public function index(AdviseRepository $adviseRepository, PaginatorInterface $paginator, Request $request, TagRepository $tagRepository): Response
    {
        $queryBuilder = $adviseRepository->findBy([], ['createdAt' => 'DESC']); // Obtenir les articles dans l'ordre décroissant de date
     

        $advises = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle
            4 // Nombre d'articles par page
        );

        // Utilisez findAllWithColors pour récupérer les tags avec couleurs
        $tags = $tagRepository->findAllWithColors();


        // Récupère tous les tags
        $tags = $tagRepository->findAll();

        // Récupérer les articles les plus populaires
        $popularAdvises = $adviseRepository->findMostPopularAdvises(5); // Limiter à 5 articles

        return $this->render('advise/display.html.twig', [
            'advises' => $advises,
            'tags' => $tags,
            'popularAdvises' => $popularAdvises, // Passez les articles populaires à la vue
        ]);
    }
    #[Route('/conseils/{slug}', name: 'advise_show')]
    public function show($slug, AdviseRepository $adviseRepository, TagRepository $tagRepository): Response
    {
        // Vérifiez si le slug est 'search' ou contient un terme de recherche
        if ($slug === 'search') {
            throw $this->createNotFoundException('Termes de recherche non valides.');
        }
    
        // Récupérer les tags
        $tags = $tagRepository->findAll();
        $tags = $tagRepository->findAllWithColors();
    
        // Récupérer les articles les plus populaires
        $popularAdvises = $adviseRepository->findMostPopularAdvises(5); // Limiter à 5 articles
    
        // Trouver l'article par son slug
        $advise = $adviseRepository->findOneBy(['slug' => $slug]);
        if (!$advise) {
            throw $this->createNotFoundException('Article non trouvé pour le slug: ' . $slug);
        }
    
        return $this->render('advise/show.html.twig', [
            'advise' => $advise,
            'tags' => $tags,
            'popularAdvises' => $popularAdvises, // Assurez-vous que c'est passé correctement
        ]);
    }
    
    #[Route('/conseils/search', name: 'advise_search')]
    public function search(Request $request, AdviseRepository $adviseRepository, TagRepository $tagRepository, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('q');
        
        if (empty($searchQuery)) {
            // Si le champ de recherche est vide, rediriger ou afficher un message
            return $this->render('advise/search.html.twig', [
                'message' => 'Veuillez entrer un terme de recherche.',
            ]);
        }
    
        // Effectuer la recherche des articles
        $advisesQuery = $adviseRepository->searchQueryBuilder($searchQuery);
        
        // Utiliser le paginator pour paginer les résultats
        $advises = $paginator->paginate(
            $advisesQuery, // la requête pour les articles
            $request->query->getInt('page', 1), // numéro de la page
            10 // nombre d'articles par page
        );
        
        // Récupérer tous les tags pour les passer à la vue
        $tags = $tagRepository->findAll();
     
        // Récupérer les articles les plus populaires
        $popularAdvises = $adviseRepository->findMostPopularAdvises(5); // Limiter à 5 articles
     
        return $this->render('advise/search.html.twig', [
            'advises' => $advises,
            'tags' => $tags,
            'popularAdvises' => $popularAdvises,
            'query' => $searchQuery,
        ]);
    }
    
    
    #[Route('/conseils/tag/{slug}', name: 'advise_by_tag')]
    public function filterByTag(string $slug, TagRepository $tagRepository, AdviseRepository $adviseRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupère le tag à partir du slug
        $tag = $tagRepository->findOneBy(['slug' => $slug]);
        if (!$tag) {
            throw $this->createNotFoundException(sprintf('Tag non trouvé pour le slug "%s"', $slug));
        }
      
        $popularAdvises = $adviseRepository->findMostPopularAdvises(5); // Limiter à 5 articles
        $queryBuilder = $adviseRepository->findByTag($tag);

        $advises = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            9 // Nombre d'articles par page
        );

        // Utilisez findAllWithColors pour récupérer les tags avec couleurs
        $tags = $tagRepository->findAllWithColors();

        // Récupère tous les tags pour le filtre
        $tags = $tagRepository->findAll();

        return $this->render('advise/display.html.twig', [
            'advises' => $advises,
            'tags' => $tags, // Passez également les tags ici
            'tag' => $tag,
            'popularAdvises' => $popularAdvises,
        ]);
    }

}
