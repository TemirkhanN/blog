<?php

namespace Frontend\Controller;

use Frontend\Resource\View\Page;
use Frontend\Service\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ErrorController
{
    public function __construct(private readonly Renderer $renderer)
    {
    }

    public function __invoke(Request $request, \Throwable $exception): Response
    {
        switch (true) {
            case $exception instanceof NotFoundHttpException:
                return $this->renderer->render(Page::ERROR, ['error' => '404']);
            case $exception instanceof AccessDeniedHttpException:
                return $this->renderer->render(Page::ERROR, ['error' => '403']);
            case $exception instanceof UnauthorizedHttpException:
                return $this->renderer->render(Page::ERROR, ['error' => '401']);
            case $exception instanceof HttpException:
                $msg = sprintf('UNEXPECTED ERROR(%d)', $exception->getStatusCode());

                return $this->renderer->render(Page::ERROR, ['error' => $msg]);
            default:
                return $this->renderer->render(Page::ERROR, ['error' => 'INTERNAL ERROR']);
        }
    }
}
