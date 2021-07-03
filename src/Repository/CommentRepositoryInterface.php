<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Comment;
use DateInterval;

/**
 * @template T of Comment
 */
interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;

    public function findCommentByGuid(string $guid): ?Comment;

    /**
     * @param string $postId
     *
     * @return Collection<T>
     */
    public function findCommentsByPost(string $postId): Collection;

    public function countCommentsInLastInterval(DateInterval $interval): int;
}
