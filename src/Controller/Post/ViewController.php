<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use App\Service\Response\ResponseFactoryInterface;
use App\View\PostView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ViewController
{
    public function __construct(
        private readonly PostListService $postListService,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null || !$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $response = $this->responseFactory->createResponse(PostView::create($post));

        return $cacheGateway->cache($response, TTL::hours(1));
    }
}
