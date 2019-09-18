<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Temirkhan\View\ViewFactoryInterface;

class ListController
{
    /**
     * @const int
     */
    private const POSTS_PER_PAGE = 10;

    /**
     * @var PostListService
     */
    private $postListService;

    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * Constructor
     *
     * @param PostListService      $postListService
     * @param ViewFactoryInterface $viewFactory
     */
    public function __construct(PostListService $postListService, ViewFactoryInterface $viewFactory)
    {
        $this->postListService = $postListService;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $posts  = $this->postListService->getPublishedPosts($offset, self::POSTS_PER_PAGE);

        $view = $this->viewFactory->createView('post.list', $posts);

        return new JsonResponse($view);
    }
}
