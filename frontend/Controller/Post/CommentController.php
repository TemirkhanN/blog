<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use Frontend\API\Client;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class CommentController
{
    private const ACTION_ADD_COMMENT = 'add';

    public function __construct(private Client $blogApi)
    {
    }

    public function __invoke(int $id, string $slug, Request $request): Response
    {
        try {
            $payload = $request->toArray();
        } catch (JsonException $e) {
            return new JsonResponse('Invalid payload', 400);
        }

        $action = (string) ($payload['action'] ?? '');
        if ($action !== self::ACTION_ADD_COMMENT) {
            return new JsonResponse('Unknown action', 400);
        }

        return $this->addComment($id, $payload);
    }

    /**
     * @param int                      $postId
     * @param array<array-key, scalar> $requestPayload
     *
     * @return Response
     */
    private function addComment(int $postId, array $requestPayload): Response
    {
        $comment = (string) ($requestPayload['comment'] ?? '');
        $replyTo = (string) ($requestPayload['replyTo'] ?? '');

        if ($replyTo === '') {
            $result = $this->blogApi->addComment($postId, $comment);
        } else {
            $result = $this->blogApi->replyToComment($replyTo, $postId, $comment);
        }

        if (!$result->isSuccessful()) {
            return new JsonResponse($result->getError()->getMessage(), 400);
        }

        return new JsonResponse();
    }
}
