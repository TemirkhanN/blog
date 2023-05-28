<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\Post\Dto\InvalidInput;
use App\Service\Response\ResponseFactoryInterface;
use App\View\ErrorView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ConvertHttpErrorToResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    /** @return array<class-string, string> */
    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class => 'onKernelError'];
    }

    public function onKernelError(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (preg_match('#^/api/.+#', $request->getPathInfo()) !== 1) {
            return;
        }

        $error = $event->getThrowable();

        switch (true) {
            case $error instanceof NotFoundHttpException:
                $response = $this->responseFactory->notFound('Resource is not found');
                break;
            case $error instanceof BadRequestHttpException:
                $response = $this->responseFactory->badRequest($error->getMessage());
                break;
            case $error instanceof AccessDeniedHttpException:
                $response = $this->responseFactory->forbidden('Forbidden access');
                break;
            case $error instanceof UnauthorizedHttpException:
                $response = $this->responseFactory->unauthorized('Authorization is required');
                break;
            case $error instanceof HttpException:
                $response = $this->responseFactory->createResponse($error->getMessage(), $error->getStatusCode());
                break;
            case $error instanceof InvalidInput:
                $response = $this->responseFactory->createResponse(ErrorView::create($error), 400);
                break;
            default:
                return;
        }

        $event->setResponse($response);
    }
}
