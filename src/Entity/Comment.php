<?php

declare(strict_types=1);

namespace App\Entity;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use Ramsey\Uuid\Uuid;

class Comment
{
    private string $guid;

    private string $comment;

    private DateTimeImmutable $createdAt;

    private Post $post;

    private ?string $repliedToCommentGuid = null;

    public static function replyTo(Comment $to, string $reply): self
    {
        $comment                       = new self($to->post, $reply);
        $comment->repliedToCommentGuid = $to->guid();

        return $comment;
    }

    public function __construct(Post $post, string $comment)
    {
        if ($comment === '') {
            throw new DomainException('Comment can not be empty.');
        }

        $this->guid      = Uuid::uuid4()->toString();
        $this->post      = $post;
        $this->comment   = $comment;
        $this->createdAt = CarbonImmutable::now();
    }

    public function guid(): string
    {
        return $this->guid;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function text(): string
    {
        return $this->comment;
    }

    public function repliedTo(): ?string
    {
        return $this->repliedToCommentGuid;
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
