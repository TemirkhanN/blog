<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Repository\CommentRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use App\Service\Response\ResponseFactoryInterface;
use App\View\CommentsTreeView;
use Symfony\Component\HttpFoundation\Response;

class ListController
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly PostRepositoryInterface $postRepository,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postRepository->findOneBySlug($slug);
        if (!$post) {
            return $this->responseFactory->notFound('Post not found');
        }

        $comments = $this->commentRepository->findCommentsByPost($post);

        $response = $this->responseFactory->createResponse(CommentsTreeView::create($comments));

        return $cacheGateway->cache($response, TTL::minutes(1));
    }
}
