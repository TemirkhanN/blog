<?php
declare(strict_types=1);

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * Json response builder
 */
class JsonResponseBuilder implements ResponseBuilderInterface
{
    /**
     * Response data
     *
     * @var mixed
     */
    private $content;

    /**
     * Response code
     *
     * @var int
     */
    private $code;

    /**
     * Response headers
     *
     * @var array
     */
    private $headers;

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
     */
    public function setContent($data): ResponseBuilderInterface
    {
        // JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT = 15
        $this->content = json_encode($data, 15);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException(json_last_error_msg());
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
     * @param string $name
     * @param array  $info
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
     * @param array $headers
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
