<?php

declare(strict_types=1);

namespace Frontend\Event;

use Frontend\API\Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AuthenticateUserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onRequest', 10],
        ];
    }

    public function __construct(private readonly Client $blogApi) {}

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $authToken = (string)$request->cookies->get('_authToken', '');
        if ($authToken !== '') {
            $this->blogApi->setUserToken($authToken);
        }
    }
}
