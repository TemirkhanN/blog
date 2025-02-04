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

    public function __invoke(int $id, CacheGatewayInterface $cacheGateway): Response
    {
        $post = $this->postRepository->findOneById($id);
        if ($post === null || !$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $response = $this->responseFactory->createResponse(PostView::create($post));

        return $cacheGateway->cache($response, TTL::hours(1));
    }
}
