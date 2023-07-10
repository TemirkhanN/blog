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
     * @internal use Post::addComment()
     */
    public static function addToPost(Post $post, string $comment): Comment
    {
        $entity = new self($post, $comment);
        CommentRepository::save($entity);

        return $entity;
    }

    private function __construct(Post $post, string $comment)
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
        CommentRepository::save($comment);

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
