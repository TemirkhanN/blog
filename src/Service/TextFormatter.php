<?php

declare(strict_types=1);

namespace App\Service;

class TextFormatter
{
    public static function cutFirstSentences(string $fromString, int $amountOfSentences): string
    {
        return implode('.', explode('.', $fromString, $amountOfSentences));
    }
}
