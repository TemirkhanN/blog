<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Domain\Repository\CommentRepositoryInterface;
use App\Dto\NewComment;
use App\Event\PostCommentedEvent;
use App\Lib\Response\Payload\SystemMessage;
use App\Lib\Response\ResponseFactoryInterface;
use App\View\CommentView;
use App\View\ValidationErrorsView;
use DateInterval;
use Ser\DtoRequestBundle\Attributes\Dto;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ReplyController
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private AuthorizationCheckerInterface $security,
        private ResponseFactoryInterface $responseFactory,
        private ValidatorInterface $validator,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(int $postId, string $replyTo, #[Dto] NewComment $commentData): Response
    {
        $commentsInLastTenMinutes = $this->commentRepository->countCommentsInInterval(new DateInterval('PT10M'));
        if ($commentsInLastTenMinutes > 10) {
            return $this->responseFactory->createResponse(new SystemMessage('Request limit match'), 429);
        }

        $target = $this->commentRepository->findCommentByGuid($replyTo);
        if ($target === null || $target->getPost()->id() !== $postId) {
            return $this->responseFactory->notFound('Target comment does not exist');
        }

        if (!$this->security->isGranted('view_post', $target->getPost())) {
            return $this->responseFactory->forbidden('You are not allowed to comment this post');
        }

        $violations = $this->validator->validate($commentData);
        if ($violations->count() !== 0) {
            return $this->responseFactory->createResponse(ValidationErrorsView::create($violations));
        }

        $reply = $target->addReply($commentData->text);
        $this->commentRepository->save($reply);
        // TODO async static events
        $this->eventDispatcher->dispatch(new PostCommentedEvent($reply));

        return $this->responseFactory->createResponse(CommentView::create($reply));
    }
}
