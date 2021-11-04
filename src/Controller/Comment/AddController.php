<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Dto\NewComment;
use App\Entity\Comment;
use App\Service\Post\CommentService;
use App\Service\Post\PostListService;
use App\Service\Response\ResponseFactoryInterface;
use App\Service\Response\Dto\SystemMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddController
{
    private PostListService $postListService;
    private CommentService $commentService;
    private ValidatorInterface $validator;
    private AuthorizationCheckerInterface $security;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        PostListService $postListService,
        CommentService $commentService,
        AuthorizationCheckerInterface $security,
        ValidatorInterface $validator,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->postListService = $postListService;
        $this->commentService  = $commentService;
        $this->validator       = $validator;
        $this->security        = $security;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(string $slug, NewComment $commentData): Response
    {
        $commentsInLastTenMinutes = $this->commentService->countCommentsInInterval(new \DateInterval('PT10M'));
        if ($commentsInLastTenMinutes > 10) {
            return $this->responseFactory->view(
                new SystemMessage('Request limit match', 429),
                'response.system_message',
                429
            );
        }

        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound('Target post does not exist');
        }

        if (!$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->forbidden('You are not allowed to comment this post');
        }

        $violations = $this->validator->validate($commentData);
        if (count($violations)) {
            return $this->responseFactory->view($violations, 'constraints.violation', Response::HTTP_BAD_REQUEST);
        }

        $comment = new Comment($post, $commentData->text);
        $this->commentService->save($comment);

        return $this->responseFactory->createResponse('', Response::HTTP_CREATED);
    }
}
