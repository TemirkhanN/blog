<?php

declare(strict_types=1);

namespace App\Event;

use App\Domain\Entity\Comment;

class PostCommentedEvent
{
    private readonly string $comment;
    private readonly string $postSlug;
    private readonly string $repliedTo;

    public function __construct(Comment $comment)
    {
        $this->comment   = $comment->text();
        $this->postSlug  = $comment->getPost()->slug();
        $this->repliedTo = $comment->repliedTo() ?? '';
    }

    public function comment(): string
    {
        return $this->comment;
    }

    public function postSlug(): string
    {
        return $this->postSlug;
    }

    public function repliedTo(): string
    {
        return $this->repliedTo;
    }
}
