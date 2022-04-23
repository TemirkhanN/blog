<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\PostData;
use App\Service\Post\PostListService;
use App\Service\Post\EditPost;
use App\Service\Response\ResponseFactoryInterface;
use DomainException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditController
{
    private EditPost $postUpdater;
    private PostListService $postListService;
    private AuthorizationCheckerInterface $security;
    private ValidatorInterface $validator;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        EditPost $postUpdater,
        PostListService $postListService,
        AuthorizationCheckerInterface $security,
        ValidatorInterface $validator,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->postUpdater     = $postUpdater;
        $this->postListService = $postListService;
        $this->security        = $security;
        $this->validator       = $validator;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(string $slug, PostData $postData): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to create posts");
        }

        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $violations = $this->validator->validate($postData);
        if (count($violations)) {
            return $this->responseFactory->view($violations, 'constraints.violation', Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->postUpdater->execute($postData, $post);
        } catch (DomainException $e) {
            return $this->responseFactory->badRequest($e->getMessage());
        }

        return $this->responseFactory->view($post, 'post.view', Response::HTTP_CREATED);
    }
}
