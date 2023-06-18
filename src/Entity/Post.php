<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Slug;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use UnexpectedValueException;

class Post
{
    public const STATE_DRAFT     = 0;
    public const STATE_PUBLISHED = 5;
    public const STATE_ARCHIVED  = 10;

    /**
     * @var int
     *
     * @phpstan-ignore-next-line
     */
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

    /**
     * @var Collection<int, Comment>
     *
     * @phpstan-ignore-next-line
     */
    private Collection $comments;

    public function __construct(string $title, string $preview, string $content)
    {
        $this->state    = self::STATE_DRAFT;
        $this->title    = $title;
        $this->preview  = $preview;
        $this->content  = $content;
        $this->comments = new ArrayCollection();
        $this->tags     = new ArrayCollection();

        $this->createdAt   = CarbonImmutable::now();
        $this->publishedAt = null;
        $this->updatedAt   = null;
        $this->slug        = (string) new Slug($this->createdAt, $title);
    }

    public function changeTitle(string $newTitle): void
    {
        $this->title     = $newTitle;
        $this->slug      = (string) new Slug($this->createdAt, $newTitle);
        $this->updatedAt = CarbonImmutable::now();
    }

    public function title(): string
    {
        return $this->title;
    }

    public function changeContent(string $newContent): void
    {
        $this->content   = $newContent;
        $this->updatedAt = CarbonImmutable::now();
    }

    public function content(): string
    {
        return $this->content;
    }

    public function changePreview(string $newPreview): void
    {
        $this->preview   = $newPreview;
        $this->updatedAt = CarbonImmutable::now();
    }

    public function preview(): string
    {
        return $this->preview;
    }

    public function slug(): string
    {
        return $this->slug;
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

    /**
     * @param string[] $tags
     *
     * @return void
     */
    public function setTags(array $tags): void
    {
        $assigningTags = array_unique($tags);
        $newTags       = array_diff($assigningTags, $this->tags());

        foreach ($this->tags as $tag) {
            if (!in_array($tag->name(), $assigningTags)) {
                $this->tags->removeElement($tag);
            }
        }

        foreach ($newTags as $newTag) {
            $this->tags->add(new Tag($newTag, $this));
        }
    }

    /**
     * @return string[]
     */
    public function tags(): array
    {
        return array_map(
            static function (Tag $tag) {
                return $tag->name();
            },
            $this->tags->toArray()
        );
    }

    public function isPublished(): bool
    {
        return $this->state === self::STATE_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->state === self::STATE_ARCHIVED;
    }

    public function publish(): void
    {
        if ($this->state === self::STATE_PUBLISHED) {
            return;
        }

        if ($this->state !== self::STATE_DRAFT) {
            $fromState = $this->getStateName($this->state);
            $toState   = $this->getStateName(self::STATE_PUBLISHED);

            throw Exception\ImpossibleTransitionException::create($fromState, $toState);
        }

        $this->state       = self::STATE_PUBLISHED;
        $this->publishedAt = CarbonImmutable::now();
        $this->updatedAt   = CarbonImmutable::now();
    }

    public function archive(): void
    {
        if ($this->state === self::STATE_ARCHIVED) {
            return;
        }

        $this->state     = self::STATE_ARCHIVED;
        $this->updatedAt = CarbonImmutable::now();
    }

    private function getStateName(int $state): string
    {
        static $statesMap = [
            self::STATE_DRAFT     => 'draft',
            self::STATE_PUBLISHED => 'published',
            self::STATE_ARCHIVED  => 'archived',
        ];

        if (!isset($statesMap[$state])) {
            throw new UnexpectedValueException(sprintf('%d is an unknown state', $state));
        }

        return $statesMap[$state];
    }
}
