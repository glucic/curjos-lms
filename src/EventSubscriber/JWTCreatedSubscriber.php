<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            JWTCreatedEvent::class => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        
        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();
        $payload['email'] = $user->getEmail();
        $payload['firstName'] = $user->getFirstName();
        $payload['lastName'] = $user->getLastName();
        $payload['roles'] = $user->getRoles();
        
        $organization = $user->getOrganization();
        if ($organization) {
            $payload['org'] = [
                'id' => $organization->getId(),
                'slug' => $organization->getSlug(),
                'name' => $organization->getName(),
            ];
        }
        
        $permissions = [];
        foreach ($user->getRoleEntities() as $role) {
            foreach ($role->getPermissions() as $permission) {
                $permissions[$permission->getName()] = true;
            }
        }
        $payload['permissions'] = array_keys($permissions);
        $event->setData($payload);
    }
}