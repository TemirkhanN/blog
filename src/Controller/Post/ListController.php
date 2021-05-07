<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use App\Service\Response\ResponseFactoryInterface;
use App\Service\Response\ValueObject\CollectionChunk;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListController
{
    private const POSTS_PER_PAGE = 10;

    private PostListService $postListService;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(PostListService $postListService, ResponseFactoryInterface $responseFactory)
    {
        $this->postListService = $postListService;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $posts  = $this->postListService->getPublishedPosts($offset, self::POSTS_PER_PAGE);

        $context = new CollectionChunk(self::POSTS_PER_PAGE, $offset, 0, $posts);

        return $this->responseFactory->view(['post.preview', $context], 'response.paginated_collection');
    }
}
