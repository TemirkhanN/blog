<?php

declare(strict_types=1);

namespace Frontend\API\Model;

use DateTimeImmutable;
use Frontend\Service\DatetimeTransformer;

readonly class Post
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public string $preview,
        public string $content,
        public array $tags,
        public array $comments,
        public DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $publishedAt,
    ) {
    }

    public static function unmarshall(array $from): self
    {
        return new self(
            $from['id'],
            $from['slug'],
            $from['title'],
            $from['preview'],
            $from['content'] ?? '',
            $from['tags'],
            $from['comments'] ?? [],
            DatetimeTransformer::transformToDateTime($from['createdAt']),
            DatetimeTransformer::transformToDateTime($from['updatedAt'] ?? null),
            DatetimeTransformer::transformToDateTime($from['publishedAt'] ?? null)
        );
    }
}
