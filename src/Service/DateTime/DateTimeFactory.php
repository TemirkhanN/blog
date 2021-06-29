<?php

declare(strict_types=1);

namespace App\Service\DateTime;

use DateTimeImmutable;
use DateTimeInterface;

final class DateTimeFactory
{
    private static ?DateTimeImmutable $stub;

    public static function now(): DateTimeImmutable
    {
        return self::$stub ?? new DateTimeImmutable();
    }

    public static function alwaysReturn(?DateTimeImmutable $stub): void
    {
        self::$stub = $stub;
    }
}
