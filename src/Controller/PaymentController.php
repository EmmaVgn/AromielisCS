<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Service\Mail;
use App\Cart\CartService;
use Stripe\Checkout\Session;
use App\Event\OrderSuccessEvent;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    protected $em;
    protected $cartService;

    public function __construct(EntityManagerInterface $em, CartService $cartService)
    {
        $this->em = $em;
        $this->cartService = $cartService;
    }

    /**
     * Etape de vérification avant confirmation du paiement
     */
    #[Route('/commande/checkout/{reference}', name: 'checkout')]
    public function payment(OrderRepository $repository, $reference, EntityManagerInterface $em): Response
    {
        // Récupération des produits de la dernière commande et formattage dans un tableau pour Stripe
        $order = $repository->findOneByReference($reference);
        if (!$order) {
            $this->addFlash('error', 'Cette commande n\'existe pas.');
            return $this->redirectToRoute('cart');
        }

        // Vérification si la commande a déjà été traitée
        if ($order->getState() != 0) {
            $this->addFlash('error', 'La commande a déjà été traitée.');
            return $this->redirectToRoute('account_orders');
        }

        $products = $order->getOrderDetails()->getValues();
        $productsForStripe = [];
        foreach ($products as $item) {
            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $item->getPrice(),
                    'product_data' => [
                        'name' => $item->getProduct()
                    ]
                ],
                'quantity' => $item->getQuantity()
            ];
        }

        // Ajout des frais de livraison
        $productsForStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName()
                ]
            ],
            'quantity' => 1
        ];

        // Charger la clé API Stripe à partir des variables d'environnement
        Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

        $YOUR_DOMAIN = 'http://localhost:8000';

        try {
            // Création de la session Stripe avec les données du panier
            $checkout_session = Session::create([
                'line_items' => $productsForStripe,
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/commande/valide/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/echec/{CHECKOUT_SESSION_ID}',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Gestion des erreurs Stripe
            $this->addFlash('error', 'Une erreur s\'est produite avec Stripe. Veuillez réessayer.');
            return $this->redirectToRoute('cart');
        }

        $order->setStripeSession($checkout_session->id);
        $em->flush();

        return $this->redirect($checkout_session->url);
    }

    /**
     * Méthode appelée lorsque le paiement est validé
     */
    #[Route('/commande/valide/{stripeSession}', name: 'payment_success')]
    public function paymentSuccess(OrderRepository $repository, $stripeSession, EntityManagerInterface $em, CartService $cart, UserRepository $user, EventDispatcherInterface $dispatcher): Response 
    {
        $order = $repository->findOneByStripeSession($stripeSession);

        if (!$order || $order->getUser() != $this->getUser() || $order->getState() == 1) {
            throw $this->createNotFoundException('Commande inaccessible ou déjà validée');
        }

        // Mettre à jour l'état de la commande si ce n'est pas déjà fait
        if (!$order->getState()) {
            $order->setState(1);  // Commande validée
            $em->flush();
        }

        // Suppression du panier une fois la commande validée
        $this->cartService->empty();

        // Lancer un événement qui permet d'envoyer un mail à la prise d'une commande
        $orderEvent = new OrderSuccessEvent($order);
        $dispatcher->dispatch($orderEvent, 'order.success');

        // 4. Je redirige avec un flash vers la liste des commandes
        $this->addFlash('success', 'La commande a été payée et confirmée');
        return $this->redirectToRoute('account_orders', ['reference' => $order->getReference()]);
    }

    /**
     * Commande annulée (clic sur retour dans la fenêtre)
     */
    #[Route('/commande/echec/{stripeSession}', name: 'payment_fail')]
    public function paymentFail(OrderRepository $repository, $stripeSession): Response
    {
        $order = $repository->findOneByStripeSession($stripeSession);
        if (!$order || $order->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('Commande inaccessible');
        }

        return $this->render('payment/fail.html.twig', [
            'order' => $order
        ]);
    }
}