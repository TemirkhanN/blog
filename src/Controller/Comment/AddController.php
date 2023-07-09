<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Event\PostCommentedEvent;
use App\Repository\CommentRepository;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\NewComment;
use App\Service\Response\Dto\SystemMessage;
use App\Service\Response\ResponseFactoryInterface;
use App\View\CommentView;
use App\View\ValidationErrorsView;
use DateInterval;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddController
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly CommentRepository $commentRepository,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ValidatorInterface $validator,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(string $slug, NewComment $commentData): Response
    {
        $commentsInLastTenMinutes = $this->commentRepository->countCommentsInInterval(new DateInterval('PT10M'));
        if ($commentsInLastTenMinutes > 10) {
            return $this->responseFactory->createResponse(new SystemMessage('Request limit match'), 429);
        }

        $post = $this->postRepository->findOneBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound('Target post does not exist');
        }

        if (!$this->security->isGranted('view_post', $post)) {
            return $this->responseFactory->forbidden('You are not allowed to comment this post');
        }

        $violations = $this->validator->validate($commentData);
        if ($violations->count() !== 0) {
            return $this->responseFactory->createResponse(ValidationErrorsView::create($violations));
        }

        $comment = $post->addComment($commentData->text);
        // TODO async static events
        $this->eventDispatcher->dispatch(new PostCommentedEvent($comment));

        return $this->responseFactory->createResponse(CommentView::create($comment));
    }
}
