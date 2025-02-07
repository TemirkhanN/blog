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

readonly class ViewController
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private AuthorizationCheckerInterface $security,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(?int $id, ?string $slug, CacheGatewayInterface $cacheGateway): Response
    {
        if ($id === null && $slug === null) {
            return $this->responseFactory->badRequest('No resource identification provided');
        }

        $post = $id !== null ? $this->postRepository->findOneById($id) : $this->postRepository->findOneBySlug($slug);
        if ($post === null || !$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $response = $this->responseFactory->createResponse(PostView::create($post));

        return $cacheGateway->cache($response, TTL::hours(1));
    }
}
