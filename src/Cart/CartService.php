<?php
namespace App\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
class CartService
{
    // public function __construct(
    //     private RequestStack $requestStack,
    // ) {
    // }
    protected $requestStack;
    protected $productRepository;
    private $cart;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
        $this->cart = [];
    }

    protected function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart);
    }

    public function add(int $id, int $quantity = 1)
    {
        $cart = $this->getCart();
    
        // Si le produit n'est pas encore dans le panier, on l'ajoute avec une quantité de 0
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }
    
        // Ajouter la quantité spécifiée
        $cart[$id] += $quantity;
    
        $this->saveCart($cart);
    }
    


    public function decrement(int $id)
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) {
            return;
        }
    
        // Si la quantité est 1, on supprime le produit
        if ($cart[$id] === 1) {
            $this->remove($id);
        } else {
            // Sinon, on décrémente la quantité
            $cart[$id]--;
            $this->saveCart($cart);
        }
    }
    

    public function remove(int $id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
    }
    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }
            $total += $product->getPrice() * $qty;
        }
        return $total;
    }
    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];
        // [12 => ['product' => '...'], 'quantity' => qté]
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }
            $detailedCart[] = new CartItem($product, $qty);
            // $detailedCart[] = [
            //     'product' => $product,
            //     'qty' => $qty
            // ];
        }
        return $detailedCart;
    }
    public function empty()
    {
        $this->saveCart([]);
    }

    public function getProductById($id)
    {
        // Retourner le produit à partir de son ID (logique à adapter)
        return $this->cart[$id] ?? null;
    }

    public function getQuantity($id)
    {
        return $this->cart[$id]['quantity'] ?? 1; // Retourner la quantité actuelle
    }

    public function incrementQuantity($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity']++;
        }
    }

    public function decrementQuantity($id)
    {
        if (isset($this->cart[$id]) && $this->cart[$id]['quantity'] > 1) {
            $this->cart[$id]['quantity']--;
        }
    }
}