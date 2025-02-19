<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Entity\Post;
use App\Domain\Repository\PostFilter;
use App\Domain\Repository\PostRepositoryInterface;
use App\Lib\Response\Cache\CacheGatewayInterface;
use App\Lib\Response\Cache\TTL;
use App\Lib\Response\Payload\CollectionChunk;
use App\Lib\Response\ResponseFactoryInterface;
use App\View\PaginatedView;
use App\View\PostView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

readonly class ListController
{
    private const POSTS_PER_PAGE = 10;

    public function __construct(
        private PostRepositoryInterface $postRepository,
        private AuthorizationCheckerInterface $security,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(Request $request, CacheGatewayInterface $cacheGateway): Response
    {
        $parseFilter = $this->buildFilter($request);
        if (!$parseFilter->isSuccessful()) {
            return $this->responseFactory->badRequest($parseFilter->getError()->getMessage());
        }
        $filter = $parseFilter->getData();

        $posts        = $this->postRepository->getPosts($filter);
        $ofTotalPosts = $this->postRepository->countPosts($filter);

        $collection = new CollectionChunk((int) $filter->limit, $filter->offset, $ofTotalPosts, $posts);
        $response   = $this->responseFactory->createResponse($this->createView($collection));

        // If accessed by admin it shouldn't be cached
        if (!$filter->onlyPublished) {
            return $response;
        }

        return $cacheGateway->cache($response, TTL::minutes(10));
    }

    /**
     * @param CollectionChunk<Post> $collection
     *
     * @return array<mixed>
     */
    private function createView(CollectionChunk $collection): array
    {
        return PaginatedView::create($collection, static fn (Post $post) => PostView::create($post, false));
    }

    /**
     * @param Request $request
     *
     * @return ResultInterface<PostFilter>
     */
    private function buildFilter(Request $request): ResultInterface
    {
        $offset = $request->query->getInt('offset', 0);
        $limit  = $request->query->getInt('limit', self::POSTS_PER_PAGE);

        if ($offset < 0) {
            return Result::error(Error::create('Offset can not be less than 0'));
        }

        if ($limit < 1 || $limit > self::POSTS_PER_PAGE * 2) {
            return Result::error(Error::create('Limit can not be less than 1 or too high'));
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

        return Result::success($filter);
    }
}
