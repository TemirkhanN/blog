<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Comment;
use App\Entity\Post;
use App\Event\PostCommentedEvent;
use App\Repository\CommentRepositoryInterface;
use DateInterval;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TemirkhanN\Generic\Collection\CollectionInterface;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param Post   $post
     * @param string $commentText
     *
     * @return ResultInterface<Comment>
     */
    public function addComment(Post $post, string $commentText): ResultInterface
    {
        $comment = new Comment($post, $commentText);

        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new PostCommentedEvent($comment));

        return Result::success($comment);
    }

    /**
     * @param Comment $replyTo
     * @param string  $commentText
     *
     * @return ResultInterface<Comment>
     */
    public function replyToComment(Comment $replyTo, string $commentText): ResultInterface
    {
        $comment = Comment::replyTo($replyTo, $commentText);

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
