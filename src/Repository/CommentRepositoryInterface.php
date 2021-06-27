<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;

    public function findCommentByGuid(string $guid): ?Comment;
}
