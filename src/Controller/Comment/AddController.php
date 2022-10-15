<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Service\Post\CommentService;
use App\Service\Post\Dto\NewComment;
use App\Service\Post\PostListService;
use App\Service\Response\Dto\SystemMessage;
use App\Service\Response\ResponseFactoryInterface;
use App\View\CommentView;
use App\View\ViolationsView;
use DateInterval;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddController
{
    public function __construct(
        private readonly PostListService $postListService,
        private readonly CommentService $commentService,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ValidatorInterface $validator,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, NewComment $commentData): Response
    {
        $commentsInLastTenMinutes = $this->commentService->countCommentsInInterval(new DateInterval('PT10M'));
        if ($commentsInLastTenMinutes > 10) {
            return $this->responseFactory->createResponse(new SystemMessage('Request limit match'), 429);
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
            return $this->responseFactory->createResponse(
                ViolationsView::create($violations),
                Response::HTTP_BAD_REQUEST
            );
        }

        $comment = $this->commentService->addComment($post, $commentData->text);

        return $this->responseFactory->createResponse(CommentView::create($comment));
    }
}
