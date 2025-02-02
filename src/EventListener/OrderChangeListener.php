<?php

namespace App\EventListener;

use App\Event\OrderChangeEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class OrderChangeListener
{
    private $mailer;
    private $twig;
    private $logger;

    public function __construct(MailerInterface $mailer, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function onOrderChange(OrderChangeEvent $event): void
    {
        $order = $event->getOrder();
        $user = $order->getUser();

        // Log l'événement
        $this->logger->info('Order status changed for order ID: ' . $order->getId());

        // Créer l'email
        $email = (new Email())
            ->from('no-reply@yourdomain.com')
            ->to($user->getEmail())
            ->subject('Mise à jour de l\'état de votre commande')
            ->html(
                $this->twig->render('emails/order_status_change.html.twig', [
                    'order' => $order,
                ])
            );

        // Envoyer l'email
        $this->mailer->send($email);
    }
}
