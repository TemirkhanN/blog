<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Temirkhan\View\ViewFactoryInterface;

class ViewController
{
    /**
     * @var PostListService
     */
    private $postListService;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $security;

    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * Constructor
     *
     * @param PostListService               $postListService
     * @param AuthorizationCheckerInterface $security
     * @param ViewFactoryInterface          $viewFactory
     */
    public function __construct(
        PostListService $postListService,
        AuthorizationCheckerInterface $security,
        ViewFactoryInterface $viewFactory
    ) {
        $this->postListService = $postListService;
        $this->security        = $security;
        $this->viewFactory     = $viewFactory;
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function __invoke(string $slug): JsonResponse
    {
        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        if (!$this->security->isGranted('view_post', $post)) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        $view = $this->viewFactory->createView('post.view', $post);

        return new JsonResponse($view);
    }
}
