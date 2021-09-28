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
    private int $id;

    private string $title;

    private string $slug;

    private DateTimeImmutable $publishedAt;

    private string $preview;

    private string $content;

    /** @var Collection<int, Tag> */
    private Collection $tags;

    /** @var Collection<int, Comment> */
    private Collection $comments;

    public function __construct(string $slug, string $title, string $preview, string $content)
    {
        $this->title       = $title;
        $this->preview     = $preview;
        $this->content     = $content;
        $publishedAt       = DateTimeFactory::now();
        $this->publishedAt = $publishedAt;
        $this->tags        = new ArrayCollection();
        $this->comments    = new ArrayCollection();
        $this->slug        = $slug;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function preview(): string
    {
        return $this->preview;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function publishedAt(): DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
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
}
