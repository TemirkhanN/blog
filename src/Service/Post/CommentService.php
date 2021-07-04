<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Collection;
use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepositoryInterface;
use DateInterval;

class CommentService
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $repository)
    {
        $this->commentRepository = $repository;
    }

    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
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
        return $this->commentRepository->findCommentsByPost($post->getSlug());
    }

    public function countCommentsInInterval(DateInterval $interval): int
    {
        return $this->commentRepository->countCommentsInLastInterval($interval);
    }
}
