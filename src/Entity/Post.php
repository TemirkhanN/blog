<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\DateTime\DateTimeFactory;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Post
{
    public const STATE_DRAFT     = 0;
    public const STATE_PUBLISHED = 5;
    public const STATE_ARCHIVED  = 10;

    private int $id;
    private int $state;
    private string $title;
    private string $slug;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $publishedAt;
    private ?DateTimeImmutable $updatedAt;
    private string $preview;
    private string $content;
    /** @var Collection<int, Tag> */
    private Collection $tags;
    /** @var Collection<int, Comment> */
    private Collection $comments;

    public function __construct(string $slug, string $title, string $preview, string $content)
    {
        $this->state       = self::STATE_DRAFT;
        $this->title       = $title;
        $this->preview     = $preview;
        $this->content     = $content;
        $this->createdAt   = DateTimeFactory::now();
        $this->publishedAt = null;
        $this->updatedAt   = null;
        $this->tags        = new ArrayCollection();
        $this->comments    = new ArrayCollection();
        $this->slug        = $slug;
    }

    public function changeTitle(string $newTitle): void
    {
        $this->title = $newTitle;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function changeContent(string $newContent): void
    {
        $this->content = $newContent;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function changePreview(string $newPreview): void
    {
        $this->preview = $newPreview;
    }

    public function preview(): string
    {
        return $this->preview;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function changeSlug(string $newSlug): void
    {
        $this->slug = $newSlug;
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function publishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function setTags(Tag ...$tags): void
    {
        $this->tags->clear();
        foreach ($tags as $tag) {
            $this->tags->add($tag);
        }
    }

    /**
     * @return Tag[]
     */
    public function tags(): array
    {
        return $this->tags->toArray();
    }

    public function isPublished(): bool
    {
        return $this->state === self::STATE_PUBLISHED;
    }

    public function publish(): void
    {
        if ($this->state === self::STATE_PUBLISHED) {
            return;
        }

        if ($this->state !== self::STATE_DRAFT) {
            throw new Exception\ImpossibleTransitionException();
        }

        $this->state       = self::STATE_PUBLISHED;
        $this->publishedAt = DateTimeFactory::now();
    }

    public function archive(): void
    {
        if ($this->state === self::STATE_ARCHIVED) {
            return;
        }

        $this->state     = self::STATE_ARCHIVED;
        $this->updatedAt = DateTimeFactory::now();
    }
}
