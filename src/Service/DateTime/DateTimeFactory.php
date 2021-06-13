<?php

declare(strict_types=1);

namespace App\Service\DateTime;

use DateTimeImmutable;
use DateTimeInterface;

final class DateTimeFactory
{
    private static ?DateTimeInterface $stub;

    public static function now(): DateTimeInterface
    {
        return self::$stub ?? new DateTimeImmutable();
    }

    public static function alwaysReturn(?DateTimeInterface $stub): void
    {
        self::$stub = $stub;
    }
}
