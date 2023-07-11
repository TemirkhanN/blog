<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Repository\ORM;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class SavePerformedChangesSubscriber implements EventSubscriberInterface
{
    private readonly ORM $orm;

    public function __construct()
    {
        $this->orm = ORM::instance();
    }

    public static function getSubscribedEvents(): array
    {
        return [TerminateEvent::class => 'onApplicationClose'];
    }

    public function onApplicationClose(): void
    {
        $this->orm->saveChanges();
    }
}
