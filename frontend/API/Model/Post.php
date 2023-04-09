<?php

declare(strict_types=1);

namespace Frontend\API\Model;

use DateTimeImmutable;
use Frontend\Service\DatetimeTransformer;

class Post
{
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly string $preview,
        public readonly string $content,
        public readonly array $tags,
        public readonly array $comments,
        public readonly DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $updatedAt,
        public readonly ?DateTimeImmutable $publishedAt,
    ) {

    }

    public static function unmarshall(array $from): self
    {
        return new self(
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
