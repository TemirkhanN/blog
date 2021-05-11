<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\Response\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ConvertHttpErrorToResponseSubscriber implements EventSubscriberInterface
{
    private ResponseFactoryInterface $responseFactory;

    private LoggerInterface $logger;

    public function __construct(ResponseFactoryInterface $responseFactory, LoggerInterface $logger)
    {
        $this->responseFactory = $responseFactory;
        $this->logger          = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => 'onKernelError',
        ];
    }

    public function onKernelError(ExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $error = $event->getException();

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
            default:
                $response = $this->responseFactory->createResponse(
                    'Internal error',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
                $this->logger->error('Unexpected error occured', ['exception' => $error]);
                break;
        }

        $event->setResponse($response);
    }
}