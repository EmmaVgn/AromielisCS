<?php 

namespace App\EventListener;

use App\Entity\Visit;
use App\Event\VisitEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class VisitListener
{
    public function __construct(private EntityManagerInterface $em) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $visit = new Visit(
            $request->getRequestUri(),
            $request->headers->get('Referer'),
            $request->getClientIp(),
            $request->headers->get('User-Agent')
        );

        $this->em->persist($visit);
        $this->em->flush();
    }
}
