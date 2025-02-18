<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface

{
    public function __construct(protected UserPasswordHasherInterface $passwordHasher)
    {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersisted',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityPersisted',
        ];
    }
    
    public function onBeforeEntityPersisted(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if (!$entity instanceof User) {
            return;
        }
        if (!is_null($entity->getPlainPassword()) && '' !== $entity->getPlainPassword()) {
            $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPlainPassword()));
        }
    }
}