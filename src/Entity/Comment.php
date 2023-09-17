<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommentRepository;
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

    /**
     * @internal For aggregate root usage only
     */
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

    public function addReply(string $reply): Comment
    {
        $comment                       = new self($this->post, $reply);
        $comment->repliedToCommentGuid = $this->guid();

        return $comment;
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
