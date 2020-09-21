<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ConvertHtppErrorToResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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

        $error   = $event->getThrowable();
        $content = [
            'error' => '',
            'code'  => $error->getCode(),
        ];

        switch (true) {
            case $error instanceof NotFoundHttpException:
                $content['error'] = 'Resource is not found.';
                $code             = Response::HTTP_NOT_FOUND;
                break;
            case $error instanceof BadRequestHttpException:
                $content['error'] = $error->getMessage();
                $code             = Response::HTTP_BAD_REQUEST;
                break;
            case $error instanceof AccessDeniedHttpException:
                $content['error'] = 'Access denied for resource or action.';
                $code             = Response::HTTP_FORBIDDEN;
                break;
            case $error instanceof UnauthorizedHttpException:
                $content['error'] = 'Authorization failure.';
                $code             = Response::HTTP_UNAUTHORIZED;
                break;
            case $error instanceof HttpException:
                $content['error'] = $error->getMessage();
                $code             = $error->getCode();
                break;
            default:
                $content['error'] = 'An error has occured';
                $code             = Response::HTTP_INTERNAL_SERVER_ERROR;
                $this->logger->error('Unexpected error occured', ['exception' => $error]);
                break;
        }

        $event->setResponse(new JsonResponse($content, $code));
    }
}