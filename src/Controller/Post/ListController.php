<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\Dto\PostFilter;
use App\Service\Post\PostListService;
use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use App\Service\Response\ResponseFactoryInterface;
use App\Service\Response\Dto\CollectionChunk;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ListController
{
    private const POSTS_PER_PAGE = 10;

    private PostListService $postListService;
    private ResponseFactoryInterface $responseFactory;
    private AuthorizationCheckerInterface $security;

    public function __construct(
        PostListService $postListService,
        AuthorizationCheckerInterface $security,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->postListService = $postListService;
        $this->security        = $security;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request, CacheGatewayInterface $cacheGateway): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $limit  = $request->query->getInt('limit', self::POSTS_PER_PAGE);

        if ($offset < 0) {
            return $this->responseFactory->badRequest('Offset can not be less than 0');
        }

        if ($limit < 1 || $limit > self::POSTS_PER_PAGE * 2) {
            return $this->responseFactory->badRequest('Limit can not be less than 1 or too high');
        }

        $filter         = new PostFilter();
        $filter->limit  = $limit;
        $filter->offset = $offset;

        $tag = $request->query->getAlnum('tag');
        if ($tag !== '') {
            $filter->tag = $tag;
        }

        if ($this->security->isGranted('create_post')) {
            $filter->onlyPublished = false;
        }

        $posts        = $this->postListService->getPosts($filter);
        $ofTotalPosts = $this->postListService->countPosts($filter);

        $context = new CollectionChunk($limit, $offset, $ofTotalPosts, $posts);

        $response = $this->responseFactory->view(['post.preview', $context], 'response.paginated_collection');

        return $cacheGateway->cache($response, TTL::minutes(10));
    }
}
