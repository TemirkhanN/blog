<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class ConvertHttpErrorToResponseSubscriber implements EventSubscriberInterface
{
    private ResponseFactoryInterface $responseFactory;
    private string $logFile;

    public function __construct(ResponseFactoryInterface $responseFactory, string $logFile)
    {
        $this->responseFactory = $responseFactory;
        $this->logFile         = $logFile;
    }

    /** @return array<class-string, string> */
    public static function getSubscribedEvents()
    {
        return [ExceptionEvent::class => 'onKernelError'];
    }

    public function onKernelError(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
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
            case $error instanceof Throwable:
                $this->logError($error);
                return;
            default:
                return;
        }

        $event->setResponse($response);
    }

    private function logError(Throwable $error): void
    {
        $msg = sprintf(
            "%s: %s in %s at %d\n\n",
            date('H:i:s d-m-Y '),
            $error->getMessage(),
            $error->getFile(),
            $error->getLine()
        );
        file_put_contents($this->logFile, $msg, FILE_APPEND);
    }
}
