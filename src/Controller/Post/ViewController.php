<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Repository\PostRepositoryInterface;
use App\Lib\Response\Cache\CacheGatewayInterface;
use App\Lib\Response\Cache\TTL;
use App\Lib\Response\ResponseFactoryInterface;
use App\View\PostView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ViewController
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postRepository->findOneBySlug($slug);
        if ($post === null || !$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $response = $this->responseFactory->createResponse(PostView::create($post));

        return $cacheGateway->cache($response, TTL::hours(1));
    }
}
