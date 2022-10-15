<?php

declare(strict_types=1);

namespace App\Service\Response;

use App\Service\Response\Dto\SystemMessage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response factory
 */
class ResponseFactory implements ResponseFactoryInterface
{
    public function __construct(private readonly ResponseBuilderInterface $builder)
    {
    }

    /**
     * Creates response
     *
     * @param mixed                   $content
     * @param int                     $statusCode
     * @param array<string, string[]> $headers
     *
     * @return Response
     */
    public function createResponse(mixed $content, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        return $this->builder
            ->setContent($content)
            ->setCode($statusCode)
            ->addHeaders($headers)
            ->build();
    }

    /**
     * Creates unauthorized access response
     *
     * @param string $details
     *
     * @return Response
     */
    public function forbidden(string $details): Response
    {
        $code = Response::HTTP_FORBIDDEN;

        return $this->createResponse(new SystemMessage($details, $code), $code);
    }

    /**
     * Creates not-found response
     *
     * @param string $details
     *
     * @return Response
     */
    public function notFound(string $details): Response
    {
        $code = Response::HTTP_NOT_FOUND;

        return $this->createResponse(new SystemMessage($details, $code), $code);
    }

    /**
     * Creates response on bad request
     *
     * @param string $details
     *
     * @return Response
     */
    public function badRequest(string $details): Response
    {
        $code = Response::HTTP_BAD_REQUEST;

        return $this->createResponse(new SystemMessage($details, $code), $code);
    }

    public function unauthorized(string $details): Response
    {
        $code = Response::HTTP_UNAUTHORIZED;

        return $this->createResponse(new SystemMessage($details, $code), $code);
    }
}
