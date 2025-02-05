<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private $mailer;
    private $templating;

    public function __construct(MailerInterface $mailer, \Twig\Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendEmail(
        string $from,
        string $name,
        string $to,
        string $subject,
        string $template,
        array $context
    ) {
        $email = new TemplatedEmail();
        $email->from(new Address($from, $name))
            ->to($to)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context)
            ->subject($subject);
      
        $this->mailer->send($email);
    }

    public function sendOrderStatusChangeEmail($to, $order)
    {

        // Création de l'email
        $email = (new Email())
            ->from('no-reply@Aromielis.com')
            ->to($to)
            ->subject('Changement d\'état de votre commande')
            ->html($this->templating->render('emails/order_status_change.html.twig', [
                'order' => $order,
                'user' => $order->getUser()
            ])); 

        // Envoi de l'email
        $this->mailer->send($email);
    }
}
