<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartQuantityFormType;
use App\Repository\ProductRepository;
use App\Form\CartConfirmationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    protected $productRepository;
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements:["id"=>"\d+"])]
    public function add($id, Request $request): Response
    {
        // Récupérer le produit via l'id
        $product = $this->productRepository->find($id);
    
        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }
    
        // Récupérer la quantité depuis la requête (méthode GET ou POST selon votre formulaire)
        $quantity = $request->get('quantity', 1);  // valeur par défaut de 1 si 'quantity' n'est pas trouvé
    
        // Ajouter le produit au panier avec la quantité spécifiée
        $this->cartService->add($id, (int)$quantity);
    
        $this->addFlash("success", "Le produit a bien été ajouté au panier :)");
    
        if ($request->query->get("returnToCart")) {
            return $this->redirectToRoute("cart_show");
        }
    
        return $this->redirectToRoute("product_show", [
            "category_slug" => $product->getCategory()->getSlug(),
            "slug" => $product->getSlug()
        ]);
    }
    

    #[Route('/cart', name: 'cart_show')]
    public function show(): Response
    {
        $form = $this->createForm(CartConfirmationFormType::class);
        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();
        
        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    #[Route('/cart/delete/{id}', name: "cart_delete", requirements: ["id" => '\d+'])]
    public function delete($id): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $this->cartService->remove($id);
        $this->addFlash('success', 'Le produit a bien été supprimé du panier');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements: ["id" => '\d+'])]
    public function decrement($id): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $this->cartService->decrement($id);
        $this->addFlash('success', 'Le produit a bien été décrémenté');
        
        return $this->redirectToRoute('cart_show');
    }

    #[Route ('/cart/update-quantity/{id}', name: 'cart_update_quantity', requirements: ['id' => '\d+'])]
    public function updateQuantity(Request $request, $id, CartService $cartService)
    {
        $product = $cartService->getProductById($id); // Récupérer le produit du panier

        // Création du formulaire pour la gestion de la quantité
        $form = $this->createForm(CartQuantityFormType::class, null, ['quantity' => $cartService->getQuantity($id)]);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'action de l'utilisateur (incrémentation ou décrémentation)
            $action = $request->request->get('action');

            if ($action === 'increment') {
                $cartService->incrementQuantity($id);
            } elseif ($action === 'decrement' && $cartService->getQuantity($id) > 1) {
                $cartService->decrementQuantity($id);
            }

            return $this->redirectToRoute('cart_show');
        }

        return $this->render('cart/update_quantity.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}