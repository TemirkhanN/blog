<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Dto\NewComment;
use App\Entity\Comment;
use App\Service\Post\CommentService;
use App\Service\Response\ResponseFactoryInterface;
use App\Service\Response\ValueObject\SystemMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReplyController
{
    private CommentService $commentService;
    private ValidatorInterface $validator;
    private AuthorizationCheckerInterface $security;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        CommentService $commentService,
        AuthorizationCheckerInterface $security,
        ValidatorInterface $validator,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->commentService  = $commentService;
        $this->validator       = $validator;
        $this->security        = $security;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(string $slug, string $replyTo, NewComment $commentData): Response
    {
        $commentsInLastTenMinutes = $this->commentService->countCommentsInInterval(new \DateInterval('PT10M'));
        if ($commentsInLastTenMinutes > 10) {
            return $this->responseFactory->view(
                new SystemMessage('Request limit match', 429),
                'response.system_message',
                429
            );
        }

        $replyToComment = $this->commentService->findCommentByGuid($replyTo);
        if (
            $replyToComment === null
            ||
            $replyToComment->getPost()->getSlug() !== $slug
        ) {
            return $this->responseFactory->notFound('Target comment does not exist');
        }

        if (!$this->security->isGranted('view_post', $replyToComment->getPost())) {
            return $this->responseFactory->forbidden("You're not allowed to comment this publication");
        }

        $violations = $this->validator->validate($commentData);
        if (count($violations)) {
            return $this->responseFactory->view($violations, 'constraints.violation', Response::HTTP_BAD_REQUEST);
        }

        $comment = Comment::replyTo($replyToComment, $commentData->text);
        $this->commentService->save($comment);

        return $this->responseFactory->createResponse('', Response::HTTP_CREATED);
    }
}
