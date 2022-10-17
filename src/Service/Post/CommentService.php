<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Comment;
use App\Entity\Post;
use App\Event\PostCommentedEvent;
use App\Repository\CommentRepositoryInterface;
use App\Service\InvalidData;
use App\Service\Post\Dto\NewComment;
use DateInterval;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TemirkhanN\Generic\Collection\CollectionInterface;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @param Post       $post
     * @param NewComment $commentData
     *
     * @return ResultInterface<Comment>
     */
    public function addComment(Post $post, NewComment $commentData): ResultInterface
    {
        $violations = $this->validator->validate($commentData);
        if ($violations->count() !== 0) {
            return Result::error(InvalidData::fromConstraintsViolation($violations));
        }

        $comment = new Comment($post, $commentData->text);

        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new PostCommentedEvent($comment));

        return Result::success($comment);
    }

    /**
     * @param Comment    $replyTo
     * @param NewComment $commentData
     *
     * @return ResultInterface<Comment>
     */
    public function replyToComment(Comment $replyTo, NewComment $commentData): ResultInterface
    {
        $violations = $this->validator->validate($commentData);
        if ($violations->count() !== 0) {
            return Result::error(InvalidData::fromConstraintsViolation($violations));
        }

        $comment = Comment::replyTo($replyTo, $commentData->text);

        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new PostCommentedEvent($comment));

        return Result::success($comment);
    }

    public function findCommentByGuid(string $guid): ?Comment
    {
        return $this->commentRepository->findCommentByGuid($guid);
    }

    /**
     * @param Post $post
     *
     * @return CollectionInterface<Comment>
     */
    public function getCommentsByPost(Post $post): CollectionInterface
    {
        return $this->commentRepository->findCommentsByPost($post);
    }

    public function countCommentsInInterval(DateInterval $interval): int
    {
        return $this->commentRepository->countCommentsInLastInterval($interval);
    }
}
