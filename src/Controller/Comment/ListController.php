<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Domain\Repository\CommentRepositoryInterface;
use App\Domain\Repository\PostRepositoryInterface;
use App\Lib\Response\Cache\CacheGatewayInterface;
use App\Lib\Response\Cache\TTL;
use App\Lib\Response\ResponseFactoryInterface;
use App\View\CommentsTreeView;
use Symfony\Component\HttpFoundation\Response;

readonly class ListController
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(int $id, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postRepository->findOneById($id);
        if (!$post) {
            return $this->responseFactory->notFound('Post not found');
        }

        $comments = $this->commentRepository->findCommentsByPost($post);

        $response = $this->responseFactory->createResponse(CommentsTreeView::create($comments));

        return $cacheGateway->cache($response, TTL::minutes(1));
    }
}
