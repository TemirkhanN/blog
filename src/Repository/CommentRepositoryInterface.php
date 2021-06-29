<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use DateInterval;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;

    public function findCommentByGuid(string $guid): ?Comment;

    public function countCommentsInLastInterval(DateInterval $interval): int;
}
