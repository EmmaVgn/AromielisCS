<?php

namespace App\EventListener;

use App\Event\OrderSuccessEvent;
use App\Service\SendMailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSuccessListener implements EventSubscriberInterface
{
    private $mailService;

    public function __construct(SendMailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderSuccessEvent::NAME => 'onOrderSuccess',
        ];
    }

    public function onOrderSuccess(OrderSuccessEvent $event)
    {
        $order = $event->getOrder();
        $user = $order->getUser();
        
        // Préparation de l'e-mail
        $context = [
            'order' => $order,
            'user' => $user,
        ];

        $this->mailService->sendEmail(
            'marie.farjaud@gmail.com',
            'Aromielis',
            $user->getEmail(),
            'Votre commande a été validée',
            'order_success', // Nom du template Twig
            $context
        );
    }
}