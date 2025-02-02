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
        $advise = $adviseRepository->findBy([], [], 3);
        $queryBuilder = $adviseRepository->findBy([], ['createdAt' => 'DESC']); // Obtenir les advises dans l'ordre décroissant de date

        $advises = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle
            4 // Nombre d'advises par page
        );

                // Utilisez findAllWithColors pour récupérer les tags avec couleurs
        $tags = $tagRepository->findAllWithColors();


        // Récupère tous les tags
        $tags = $tagRepository->findAll();

        // Récupérer les advises les plus populaires
        $popularAdvises = $adviseRepository->findMostPopularAdvises(5); // Limiter à 5 advises

        return $this->render('advise/display.html.twig', [
            'advises' => $advises,
            'tags' => $tags,
            'popularadvises' => $popularAdvises, // Passez les advises populaires à la vue
        ]);

        // Récupère tous les tags
        $tags = $tagRepository->findAll();
        return $this->render('advise/display.html.twig', [
            'advises' => $advises,
            'tags' => $tags, // Passe les tags à la vue
        ]);

        return $this->render('advise/index.html.twig', [
            'controller_name' => 'AdviseController',
            'advise' => $advise,
            
        ]);
    }

    #[Route('/conseils/{slug}', name: 'advise_show')]
    public function show($slug, AdviseRepository $adviseRepository): Response
    {
        dump($slug);  // Ajoutez cette ligne pour vérifier le slug
        $advise = $adviseRepository->findOneBy(['slug' => $slug]);
    
        if (!$advise) {
            throw $this->createNotFoundException('The advise does not exist');
        }
    
        return $this->render('advise/show.html.twig', [
            'advise' => $advise,
        ]);
    }
    
    


    #[Route('/conseils/search', name: 'advise_search')]
    public function search(Request $request, AdviseRepository $adviseRepository, TagRepository $tagRepository, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('q');
    
        // Utiliser le QueryBuilder pour récupérer les advises correspondant à la recherche
        $advisesQuery = $adviseRepository->searchQueryBuilder($searchQuery);
    
        // Utiliser le paginator pour paginer les résultats
        $advises = $paginator->paginate(
            $advisesQuery, // le QueryBuilder retourné
            $request->query->getInt('page', 1), // numéro de la page
            10 // nombre d'advises par page
        );
    
        // Récupérer tous les tags pour les passer à la vue
        $tags = $tagRepository->findAll();
    
        // Récupérer les advises populaires
        $popularAdvises = $adviseRepository->findMostPopularAdvises(5); // Limiter à 5 advises
    
        return $this->render('blog/search.html.twig', [
            'advises' => $advises, // advises paginés
            'tags' => $tags,
            'popularAdvisees' => $popularAdvises,
            'query' => $searchQuery,
        ]);
    }
    
    #[Route('/conseils/tag/{slug}', name: 'advise_by_tag')]
    public function filterByTag(string $slug, TagRepository $tagRepository, AdviseRepository $adviseRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupère le tag à partir du slug
        $tag = $tagRepository->findOneBy(['slug' => $slug]);
        if (!$tag) {
            throw $this->createNotFoundException('Tag non trouvé');
        }
    
        $queryBuilder = $adviseRepository->findByTag($tag);
    
        $advises = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            9 // Nombre d'advises par page
        );
    
        // Récupère tous les tags pour le filtre
        $tags = $tagRepository->findAll();
    
        return $this->render('advise/display.html.twig', [
            'advises' => $advises,
            'tags' => $tags, // Passez également les tags ici
            'tag' => $tag,
        ]);
    }

}
