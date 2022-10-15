<?php

declare(strict_types=1);

namespace App\View;

use DateTimeInterface;

class DateTimeView
{
    public static function create(DateTimeInterface $dateTime, ?string $format = null): string
    {
        return $dateTime->format($format ?? DATE_W3C);
    }
}
