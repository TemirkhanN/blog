<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Service\Post\CommentService;
use App\Service\Post\PostListService;
use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use App\Service\Response\ResponseFactoryInterface;
use App\View\CommentsTreeView;
use Symfony\Component\HttpFoundation\Response;

class ListController
{
    public function __construct(
        private readonly CommentService $commentService,
        private readonly PostListService $postListService,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postListService->getPostBySlug($slug);
        if (!$post) {
            return $this->responseFactory->notFound('Post not found');
        }

        $comments = $this->commentService->getCommentsByPost($post);

        $response = $this->responseFactory->createResponse(CommentsTreeView::create($comments));

        return $cacheGateway->cache($response, TTL::minutes(1));
    }
}
