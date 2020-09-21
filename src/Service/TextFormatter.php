<?php

declare(strict_types=1);

namespace App\Service;

class TextFormatter
{
    public static function cutFirstSentences(string $fromString, int $amountOfSentences): string
    {
        if ($amountOfSentences < 1) {
            throw new \UnexpectedValueException('Amount of the sentences can not be lesser than 1.');
        }

        $allSentences = explode('.', $fromString);
        $amountOfAllSentences = count($allSentences);

        if ($amountOfSentences >= $amountOfAllSentences) {
            return $fromString;
        }

        $sentences = [];
        for ($i = 0; $i < $amountOfSentences && $i < $amountOfAllSentences; $i++) {
            $sentences[] = $allSentences[$i];
        }

        return implode('.', $sentences) . '...';
    }
}
