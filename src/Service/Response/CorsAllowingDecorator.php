<?php

declare(strict_types=1);

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\Response;

class CorsAllowingDecorator implements ResponseFactoryInterface
{
    public function __construct(
        private readonly ?string $allowedOrigin,
        private readonly ResponseFactoryInterface $factory
    ) {
    }

    public function createResponse($content, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        $response = $this->factory->createResponse($content, $statusCode, $headers);
        $this->addOriginHeader($response);

        return $response;
    }

    public function forbidden(string $details): Response
    {
        $response = $this->factory->forbidden($details);
        $this->addOriginHeader($response);

        return $response;
    }

    public function notFound(string $details): Response
    {
        $response = $this->factory->notFound($details);
        $this->addOriginHeader($response);

        return $response;
    }

    public function badRequest(string $message): Response
    {
        $response = $this->factory->badRequest($message);
        $this->addOriginHeader($response);

        return $response;
    }

    public function unauthorized(string $message): Response
    {
        $response = $this->factory->unauthorized($message);
        $this->addOriginHeader($response);

        return $response;
    }

    private function addOriginHeader(Response $response): void
    {
        if ($this->allowedOrigin === null) {
            return;
        }

        if ($response->headers->has('Access-Control-Allow-Origin')) {
            return;
        }

        $response->headers->set('Access-Control-Allow-Origin', $this->allowedOrigin);
    }
}
