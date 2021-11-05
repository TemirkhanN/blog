<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Service\Post\CommentService;
use App\Service\Post\PostListService;
use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

class ListController
{
    private CommentService $commentService;
    private PostListService $postListService;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        CommentService $commentService,
        PostListService $postListService,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->commentService  = $commentService;
        $this->postListService = $postListService;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(string $slug, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postListService->getPostBySlug($slug);
        if (!$post) {
            return $this->responseFactory->notFound('Post not found');
        }

        $comments = $this->commentService->getCommentsByPost($post);

        $response = $this->responseFactory->view($comments, 'post.comments');

        return $cacheGateway->cache($response, TTL::minutes(1));
    }
}
