<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Publication
 */
class Post
{
    private string $title;

    private string $slug;

    private DateTimeImmutable $publishedAt;

    private string $content;

    /** @var Collection<Tag> */
    private Collection $tags;

    public function __construct(string $title, string $content)
    {
        $this->title       = $title;
        $this->content     = $content;
        $this->publishedAt = new DateTimeImmutable();
        $this->tags        = new ArrayCollection();
        $this->slug        = sprintf(
            '%s_%s',
            $this->getPublishedAt()->format('Y-m-d'),
            (string)preg_replace('#\W#u', '-', $title)
        );
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPublishedAt(): DateTimeInterface
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
    public function getTags(): array
    {
        return $this->tags->toArray();
    }
}
