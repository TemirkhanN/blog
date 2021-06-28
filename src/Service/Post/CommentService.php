<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Comment;
use App\Repository\CommentRepositoryInterface;

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
}