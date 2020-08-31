<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\PostListService;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * Constructor
     *
     * @param PostListService               $postListService
     * @param AuthorizationCheckerInterface $security
     * @param ResponseFactoryInterface      $responseFactory
     */
    public function __construct(
        PostListService $postListService,
        AuthorizationCheckerInterface $security,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->postListService = $postListService;
        $this->security        = $security;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $slug
     *
     * @return Response
     */
    public function __invoke(string $slug): Response
    {
        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        if (!$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->forbidden("You're not allowed to view this publication");
        }

        return $this->responseFactory->view($post, 'post.preview');
    }
}
