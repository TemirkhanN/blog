<?php

declare(strict_types=1);

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
                return $this->notFound();
            case $exception instanceof AccessDeniedHttpException:
                return $this->forbidden();
            case $exception instanceof UnauthorizedHttpException:
                return $this->unauthorized();
            case $exception instanceof HttpException:
                $msg = sprintf('UNEXPECTED ERROR(%d)', $exception->getStatusCode());

                return new Response(
                    $this->renderer->render(Page::ERROR, ['error' => $msg]),
                    $exception->getStatusCode()
                );
            default:
                return new Response($this->renderer->render(Page::ERROR, ['error' => 'INTERNAL ERROR']), 500);
        }
    }

    public function notFound(): Response
    {
        return new Response($this->renderer->render(Page::ERROR, ['error' => '404']), 404);
    }

    public function forbidden(): Response
    {
        return new Response($this->renderer->render(Page::ERROR, ['error' => '403']), 403);
    }

    public function unauthorized(): Response
    {
        return new Response($this->renderer->render(Page::ERROR, ['error' => '401']), 401);
    }
}
