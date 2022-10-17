<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use App\Service\Post\PublishPost;
use App\Service\Response\Dto\SystemMessage;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PublishController
{
    public function __construct(
        private readonly PostListService $postListService,
        private readonly AuthorizationCheckerInterface $security,
        private readonly PublishPost $publisher,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to modify posts");
        }

        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $result = $this->publisher->execute($post);
        if (!$result->isSuccessful()) {
            return $this->responseFactory->createResponse(new SystemMessage($result->getError()->getMessage()));
        }

        return $this->responseFactory->createResponse('');
    }
}
