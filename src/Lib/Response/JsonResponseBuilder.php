<?php

declare(strict_types=1);

namespace App\Lib\Response;

use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

/**
 * Json response builder
 */
class JsonResponseBuilder implements ResponseBuilderInterface
{
    private ?string $content;

    /**
     * Response code
     *
     * @var int
     */
    private int $code;

    /**
     * Response headers
     *
     * @var array<string, string[]>
     */
    private array $headers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Sets response data
     *
     * @param mixed $data
     *
     * @return ResponseBuilderInterface
     *
     * @throws UnexpectedValueException
     */
    public function setContent(mixed $data): ResponseBuilderInterface
    {
        // JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT = 15
        $this->content = (string) json_encode($data, 15);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException(json_last_error_msg());
        }

        return $this;
    }

    /**
     * Sets status code
     *
     * @param int $statusCode
     *
     * @return ResponseBuilderInterface
     */
    public function setCode(int $statusCode): ResponseBuilderInterface
    {
        $this->code = $statusCode;

        return $this;
    }

    /**
     * Adds response header info
     *
     * @param string   $name
     * @param string[] $info
     *
     * @return ResponseBuilderInterface
     */
    public function addHeader(string $name, array $info): ResponseBuilderInterface
    {
        $this->headers[$name] = $info;

        return $this;
    }

    /**
     * Adds multiple headers
     *
     * @param array<string, string[]> $headers
     *
     * @return ResponseBuilderInterface
     */
    public function addHeaders(array $headers): ResponseBuilderInterface
    {
        foreach ($headers as $header => $info) {
            $this->addHeader($header, $info);
        }

        return $this;
    }

    /**
     * Builds response
     *
     * @return Response
     */
    public function build(): Response
    {
        $response = new Response($this->content, $this->code, $this->headers);

        $this->reset();

        return $response;
    }

    /**
     * Resets payload
     */
    public function reset(): void
    {
        $this->content = null;
        $this->code    = Response::HTTP_OK;
        $this->headers = [
            'Content-Type' => ['application/json'],
        ];
    }
}
