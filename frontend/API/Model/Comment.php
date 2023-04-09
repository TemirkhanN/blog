<?php
declare(strict_types=1);

namespace Frontend\API\Model;

use DateTimeImmutable;
use Frontend\Service\DatetimeTransformer;

class Comment
{
    public function __construct(
        public readonly string $guid,
        public readonly DateTimeImmutable $createdAt,
        public readonly string $text,
        public readonly array $replies,
        public readonly string $repliedTo
    ) {}

    public static function unmarshall(array $from): self
    {
        return new self(
            $from['guid'],
            DatetimeTransformer::transformToDateTime($from['createdAt']),
            $from['comment'],
            array_map([self::class, 'unmarshall'], $from['replies']),
            $from['repliedTo'] ?? ''
        );
    }
}
