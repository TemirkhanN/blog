<?php

declare(strict_types=1);

namespace App\Event;

use App\Domain\Entity\Comment;

readonly class PostCommentedEvent
{
    public string $comment;
    public string $postSlug;
    public string $repliedTo;
    public int $postId;

    public function __construct(Comment $comment)
    {
        $this->comment   = $comment->text();
        $this->repliedTo = $comment->repliedTo() ?? '';
        $this->postSlug  = $comment->getPost()->slug();
        $this->postId    = $comment->getPost()->id();
    }
}
