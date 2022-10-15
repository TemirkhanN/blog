<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Service\Post\CommentService;
use App\Service\Post\Dto\NewComment;
use App\Service\Response\Dto\SystemMessage;
use App\Service\Response\ResponseFactoryInterface;
use App\View\CommentView;
use App\View\ViolationsView;
use DateInterval;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReplyController
{
    public function __construct(
        private readonly CommentService $commentService,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ValidatorInterface $validator,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, string $replyTo, NewComment $commentData): Response
    {
        $commentsInLastTenMinutes = $this->commentService->countCommentsInInterval(new DateInterval('PT10M'));
        if ($commentsInLastTenMinutes > 10) {
            return $this->responseFactory->createResponse(new SystemMessage('Request limit match'), 429);
        }

        $replyToComment = $this->commentService->findCommentByGuid($replyTo);
        if (
            $replyToComment === null
            ||
            $replyToComment->getPost()->slug() !== $slug
        ) {
            return $this->responseFactory->notFound('Target comment does not exist');
        }

        if (!$this->security->isGranted('view_post', $replyToComment->getPost())) {
            return $this->responseFactory->forbidden("You're not allowed to comment this publication");
        }

        $violations = $this->validator->validate($commentData);
        if (count($violations)) {
            return $this->responseFactory->createResponse(
                ViolationsView::create($violations),
                Response::HTTP_BAD_REQUEST
            );
        }

        $comment = $this->commentService->replyToComment($replyToComment, $commentData->text);

        return $this->responseFactory->createResponse(CommentView::create($comment));
    }
}
