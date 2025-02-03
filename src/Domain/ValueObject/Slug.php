<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Carbon\Carbon;
use DateTimeInterface;

class Slug
{
    private string $value;

    public function __construct(string $title, ?DateTimeInterface $dateTime = null)
    {
        if ($dateTime === null) {
            $dateTime = Carbon::now();
        }

        $this->value = sprintf('%s_%s', $dateTime->format('Y-m-d'), $this->convertChars($title));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function convertChars(string $text): string
    {
        return (string) preg_replace('#\W#u', '-', $text);
    }
}
