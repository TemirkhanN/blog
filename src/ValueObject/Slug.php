<?php

declare(strict_types=1);

namespace App\ValueObject;

use DateTimeInterface;

class Slug
{
    private string $value;

    public function __construct(DateTimeInterface $dateTime, string $title)
    {
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
