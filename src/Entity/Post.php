<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\DateTime\DateTimeFactory;
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

    private string $preview;

    private string $content;

    /** @var Collection<Tag> */
    private Collection $tags;

    public function __construct(string $title, string $preview, string $content)
    {
        $this->title       = $title;
        $this->preview     = $preview;
        $this->content     = $content;
        $format            = DateTimeInterface::ATOM;
        $this->publishedAt = DateTimeImmutable::createFromFormat($format, DateTimeFactory::now()->format($format));
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

    public function getPreview(): string
    {
        return $this->preview;
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
