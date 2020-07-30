<?php
declare(strict_types=1);

namespace App\Service\Response;

use App\Service\Response\ValueObject\SystemMessage;
use Symfony\Component\HttpFoundation\Response;
use Temirkhan\View\Exception\UnknownViewException;
use Temirkhan\View\ViewFactoryInterface;

/**
 * Response factory
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * View factory
     *
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * Response builder
     *
     * @var ResponseBuilderInterface
     */
    private $builder;

    /**
     * Constructor
     *
     * @param ResponseBuilderInterface $builder
     * @param ViewFactoryInterface     $viewFactory
     */
    public function __construct(ResponseBuilderInterface $builder, ViewFactoryInterface $viewFactory)
    {
        $this->builder     = $builder;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Creates response
     *
     * @param mixed $content
     * @param int   $statusCode
     * @param array $headers
     *
     * @return Response
     */
    public function createResponse($content, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        return $this->builder
            ->setContent($content)
            ->setCode($statusCode)
            ->addHeaders($headers)
            ->build();
    }

    /**
     * Views response with data representation
     *
     * @param mixed  $data
     * @param string $representationName
     * @param int    $statusCode
     *
     * @return Response
     */
    public function view($data, string $representationName, int $statusCode = Response::HTTP_OK): Response
    {
        try {
            $content = $this->viewFactory->createView($representationName, $data);
        } catch (UnknownViewException $e) {
            $content = null;
        }

        return $this->createResponse($content, $statusCode);
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
        $message = new SystemMessage($details, Response::HTTP_FORBIDDEN);

        return $this->view($message, 'response.system_message', Response::HTTP_FORBIDDEN);
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
        $message = new SystemMessage($details, Response::HTTP_NOT_FOUND);

        return $this->view($message, 'response.system_message', Response::HTTP_NOT_FOUND);
    }

    /**
     * Creates response on bad request
     *
     * @param string $message
     *
     * @return Response
     */
    public function badRequest(string $message): Response
    {
        return $this->createResponse($message, Response::HTTP_BAD_REQUEST);
    }
}
