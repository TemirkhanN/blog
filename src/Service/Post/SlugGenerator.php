<?php

declare(strict_types=1);

namespace App\Service\Post;

class SlugGenerator
{
    public function generate(string $title): string
    {
        return sprintf('%s_%s', date('Y-m-d'), $this->convertChars($title));
    }

    public function regenerate(string $oldSlug, string $newTitle): string
    {
        $dateFromOldSlug = mb_substr($oldSlug, 0, 10);

        return sprintf('%s_%s', $dateFromOldSlug, $this->convertChars($newTitle));
    }

    private function convertChars(string $text): string
    {
        return (string) preg_replace('#\W#u', '-', $text);
    }
}
