<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Comment;
use App\Domain\Entity\Post;
use DateInterval;
use TemirkhanN\Generic\Collection\CollectionInterface;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;

    public function findCommentByGuid(string $guid): ?Comment;

    /**
     * @param Post $post
     *
     * @return CollectionInterface<Comment>
     */
    public function findCommentsByPost(Post $post): CollectionInterface;

    public function countCommentsInInterval(DateInterval $interval): int;
}
