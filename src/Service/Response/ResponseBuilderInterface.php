<?php

declare(strict_types=1);

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * Response builder interface
 */
interface ResponseBuilderInterface
{
    /**
     * Sets response content
     *
     * @param mixed $data
     *
     * @return ResponseBuilderInterface
     */
    public function setContent(mixed $data): ResponseBuilderInterface;

    /**
     * Sets status code
     *
     * @param int $statusCode
     *
     * @return ResponseBuilderInterface
     */
    public function setCode(int $statusCode): ResponseBuilderInterface;

    /**
     * Adds header info
     *
     * @param string   $name
     * @param string[] $info
     *
     * @return ResponseBuilderInterface
     */
    public function addHeader(string $name, array $info): ResponseBuilderInterface;

    /**
     * Adds multiple headers
     *
     * @param array<string, string[]> $headers
     *
     * @return ResponseBuilderInterface
     */
    public function addHeaders(array $headers): ResponseBuilderInterface;

    /**
     * Builds response
     *
     * @return Response
     */
    public function build(): Response;
}
