<?php

declare(strict_types=1);

namespace Frontend\Service;

use DateTimeImmutable;

class DatetimeTransformer
{
    public static function transformToDateTime(?string $raw): ?DateTimeImmutable
    {
        if ($raw === null) {
            return null;
        }

        return new DateTimeImmutable($raw);
    }
}
