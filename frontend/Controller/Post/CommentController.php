<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use Frontend\API\Client;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController
{
    private const ACTION_ADD_COMMENT = 'add';

    public function __construct(private readonly Client $blogApi)
    {
    }

    public function __invoke(string $postSlug, Request $request): Response
    {
        try {
            $payload = $request->toArray();
        } catch (JsonException $e) {
            return new JsonResponse('Invalid payload', 400);
        }

        $action = (string) ($payload['action'] ?? '');

        switch ($action) {
            case self::ACTION_ADD_COMMENT:
                return $this->addComment($postSlug, $payload);
            default:
                return new JsonResponse('Unknown action', 400);
        }
    }

    /**
     * @param string                   $postSlug
     * @param array<array-key, scalar> $requestPayload
     *
     * @return Response
     */
    private function addComment(string $postSlug, array $requestPayload): Response
    {
        $comment = (string) ($requestPayload['comment'] ?? '');
        $replyTo = (string) ($requestPayload['replyTo'] ?? '');

        if ($replyTo === '') {
            $result = $this->blogApi->addComment($postSlug, $comment);
        } else {
            $result = $this->blogApi->replyToComment($replyTo, $postSlug, $comment);
        }

        if (!$result->isSuccessful()) {
            return new JsonResponse($result->getError()->getMessage(), 400);
        }

        return new JsonResponse();
    }
}
