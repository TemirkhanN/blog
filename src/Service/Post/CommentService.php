<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Collection;
use App\Entity\Comment;
use App\Entity\Post;
use App\Event\PostCommentedEvent;
use App\Repository\CommentRepositoryInterface;
use DateInterval;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
        $this->eventDispatcher->dispatch(new PostCommentedEvent($comment));
    }

    public function findCommentByGuid(string $guid): ?Comment
    {
        return $this->commentRepository->findCommentByGuid($guid);
    }

    /**
     * @param Post $post
     *
     * @return Collection<Comment>
     */
    public function getCommentsByPost(Post $post): Collection
    {
        return $this->commentRepository->findCommentsByPost($post);
    }

    public function countCommentsInInterval(DateInterval $interval): int
    {
        return $this->commentRepository->countCommentsInLastInterval($interval);
    }
}
