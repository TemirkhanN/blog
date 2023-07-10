<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class SavePerformedChangesSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ManagerRegistry $persistenceRegistry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [TerminateEvent::class => 'onApplicationClose'];
    }

    public function onApplicationClose(): void
    {
        foreach ($this->persistenceRegistry->getManagers() as $manager) {
            $manager->flush();
        }
    }
}
