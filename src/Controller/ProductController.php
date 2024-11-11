<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{slug}', name: 'product_category', priority: -1)]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority: -1)]
    public function show(
        ProductRepository $productRepository,
        Request $request,
        string $slug,
        string $category_slug,
        EntityManagerInterface $em
    ): Response {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException("La page demandée n'existe pas");
        }

    return $this->render('product/show.html.twig', [
        'product' => $product,
        'category_slug' => $category_slug
    ]);
}

    #[Route('/produits', name: 'product_display')]
    public function display(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

  
 

        [$minPrice, $maxPrice] = $productRepository->findMinMaxPrice($data);
        $products = $productRepository->findSearch($data);
        $totalItems = $productRepository->countItems($data);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('product/_products.html.twig', ['products' => $products]),
                'sorting' => $this->renderView('product/_sorting.html.twig', ['products' => $products]),
                'pagination' => $this->renderView('product/_pagination.html.twig', ['products' => $products]),
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
            ]);
        }

        return $this->render('product/display.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'totalItems' => $totalItems,
        ]);
    }

    public function list(Request $request, PaginatorInterface $paginator, ProductRepository $productRepository): Response
    {
        // Créez une requête pour récupérer les produits
        $queryBuilder = $productRepository->createQueryBuilder('p');

        // Paginer les résultats de la requête
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/,
            
        );

        // Rendre le template avec les produits paginés
        return $this->render('product/list.html.twig', [
            'products' => $pagination,
        ]);
    }
}

