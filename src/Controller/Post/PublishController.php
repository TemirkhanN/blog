<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Entity\Exception\ImpossibleTransitionException;
use App\Service\Post\PostListService;
use App\Service\Post\PublishPost;
use App\Service\Response\Dto\SystemMessage;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PublishController
{
    private PostListService $postListService;
    private AuthorizationCheckerInterface $security;
    private PublishPost $publisher;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        PostListService $postListService,
        AuthorizationCheckerInterface $security,
        PublishPost $publisher,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->postListService = $postListService;
        $this->security        = $security;
        $this->publisher       = $publisher;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(string $slug): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to modify posts");
        }

        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $result = $this->publisher->execute($post);
        if (!$result->isSuccessful()) {
            return $this->responseFactory->view(new SystemMessage($result->getError(), 0), 'response.system_message');
        }

        return $this->responseFactory->createResponse('');
    }
}
