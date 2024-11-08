<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Cart\CartService;
use App\Form\OrderFormType;
use App\Entity\OrderDetails;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * Récupération du panier, choix de l'adresse et du transporteur
     *
     * @param SessionInterface $session
     * @param Cart $cart
     * @return Response
     */
    #[Route('/commande', name: 'order')]
    public function index(SessionInterface $session, CartService $cart, UserRepository $userRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        $cartProducts = $cart->getDetailedCartItems();

        // Redirection si panier vide
        if (empty($cartProducts)) {
            return $this->redirectToRoute('product_display');
        }
            
        // Redirection si l'utilisateur n'a pas encore d'adresse
        if ($user->getAddresses()->isEmpty()) { 
            $session->set('order', 1);
            return $this->redirectToRoute('account_address_new');
        }

        
        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $user     //Permet de passer l'utilisateur courant dans le tableau d'options du OrderType
        ]); 

        // Create an instance of the Order entity
        $order = new Order();
        $order->setUser($user);
        $order->calculateCarrierPrice($cart->getTotal() / 100);
    
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartProducts,
            'totalPrice' => $cart->getTotal(),
            'order' => $order // Pass the order object to the template
        ]);
    }

    /**
     * Enregistrement des données "en dur" de la commande contenant adresse, transporteur et produits
     * Les relations ne sont pas directement utilisées pour la persistance des données dans les entités Order et OrderDetails
     * pour éviter des incohérences dans le cas ou des modifications seraient faites sur les autres entités par la suite
     *
     * @param Cart $cart
     * @param Request $request
     * @return Response
     */
    #[Route('/commande/recap', name: 'order_add', methods: 'POST')]
    public function summary(CartService $cart, Request $request, EntityManagerInterface $em): Response
    {
        
        $cartProducts = $cart->getDetailedCartItems();
        $totalPrice = $cart->getTotal(); // Total price of the cart

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->get('addresses')->getData();
            $carrier = $form->get('carriers')->getData();

            // Déterminer si la livraison est gratuite
            $isFreeShipping = ($totalPrice / 100) > 49;
            $carrierPrice = $isFreeShipping ? 0 : $carrier->getPrice();

            $deliveryString = sprintf(
                '%s %s<br>%s<br>%s%s<br>%s<br>%s<br>%s',
                $address->getFirstname(),
                $address->getLastname(),
                $address->getPhone(),
                $address->getCompany() ? $address->getCompany() . '<br>' : '',
                $address->getAddress(),
                $address->getPostal(),
                $address->getCity(),
                $address->getCountry()
            );

            $order = new Order();
            $order->setUser($this->getUser())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setCarrierName($carrier->getName())
                ->setCarrierPrice($carrierPrice)
                ->setDelivery($deliveryString)
                ->setState(0)
                ->setReference((new \DateTime())->format('YmdHis') . '-' . uniqid())
                ->setTotal($totalPrice); // Set the total price here

            $em->persist($order);

            foreach ($cartProducts as $item) {
                $orderDetails = new OrderDetails();
                $orderDetails->setBindedOrder($order)
                    ->setProduct($item->getProduct()->getName())
                    ->setQuantity($item->getQuantity())
                    ->setPrice($item->getProduct()->getPrice())
                    ->setTotal($item->getTotal());

                $em->persist($orderDetails);
            }

            $em->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cartProducts,
                'totalPrice' => $totalPrice,
                'order' => $order
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}