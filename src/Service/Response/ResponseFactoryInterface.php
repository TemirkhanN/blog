<?php

declare(strict_types=1);

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * Response factory interface
 */
interface ResponseFactoryInterface
{
    /**
     * Creates response
     *
     * @param mixed                   $content
     * @param int                     $statusCode
     * @param array<string, string[]> $headers
     *
     * @return Response
     */
    public function createResponse(mixed $content, int $statusCode = Response::HTTP_OK, array $headers = []): Response;

    /**
     * Creates response with data represented
     *
     * @param mixed  $data
     * @param string $representationName
     * @param int    $statusCode
     *
     * @return Response
     */
    public function view(mixed $data, string $representationName, int $statusCode = Response::HTTP_OK): Response;

    /**
     * Creates forbidden access response
     *
     * @param string $details
     *
     * @return Response
     */
    public function forbidden(string $details): Response;

    /**
     * Creates not-found response
     *
     * @param string $details
     *
     * @return Response
     */
    public function notFound(string $details): Response;

    public function badRequest(string $message): Response;

    public function unauthorized(string $message): Response;
}
