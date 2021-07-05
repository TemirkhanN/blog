<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Comment;
use App\Entity\Post;
use DateInterval;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;

    public function findCommentByGuid(string $guid): ?Comment;

    /**
     * @param Post $post
     *
     * @return Collection<Comment>
     */
    public function findCommentsByPost(Post $post): Collection;

    public function countCommentsInLastInterval(DateInterval $interval): int;
}
