<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Carbon\Carbon;
use DateTimeInterface;

readonly class Slug
{
    public static function create(string $title, ?DateTimeInterface $dateTime = null): string
    {
        if ($dateTime === null) {
            $dateTime = Carbon::now();
        }

        return sprintf('%s_%s', $dateTime->format('Y-m-d'), self::convertChars($title));
    }

    private static function convertChars(string $text): string
    {
        return (string) preg_replace('#\W#u', '-', $text);
    }
}
