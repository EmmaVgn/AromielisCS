<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority: -1)]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority: -1)]
    public function show($category_slug, $slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$product) {
            throw $this->createNotFoundException("Le véhicule demandé n'existe pas");
        }
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/produits', name: 'product_display')]
    public function display(ProductRepository $productRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $data->category = $request->get('category');  // This assumes category is passed by its ID or slug
        $data->minPrice = $request->get('minPrice');
        $data->maxPrice = $request->get('maxPrice');
    
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
    
        // Fetch products based on the filters
        $pagination = $productRepository->findSearch($data);
    
        // Get min and max prices if needed
        [$minPrice, $maxPrice] = $productRepository->findMinMaxPrice($data);
    
        return $this->render('product/display.html.twig', [
            'products' => $pagination,
            'form' => $form->createView(),
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }    
    
}
