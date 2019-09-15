<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use Symfony\Component\HttpFoundation\JsonResponse;
use Temirkhan\View\ViewFactoryInterface;

class ListController
{
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

    public function __invoke()
    {
        $offset = 0;
        $limit = 25;
        $posts = $this->postListService->getPublishedPosts($offset, $limit);

        $view = $this->viewFactory->createView('post.list', $posts);

        return new JsonResponse($view);
    }
}
