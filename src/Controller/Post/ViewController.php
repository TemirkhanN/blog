<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ViewController
{
    private PostListService $postListService;

    private AuthorizationCheckerInterface $security;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        PostListService $postListService,
        AuthorizationCheckerInterface $security,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->postListService = $postListService;
        $this->security        = $security;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(string $slug, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null || !$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $response = $this->responseFactory->view($post, 'post.view');

        return $cacheGateway->cache($response, TTL::hours(1));
    }
}
