<?php

declare(strict_types=1);

namespace App\Service;

class UriResolver
{
    public function __construct(private readonly string $host)
    {
    }

    public function resolvePostUriBySlug(string $postSlug): string
    {
        return sprintf('%s/blog/%s', $this->host, $postSlug);
    }

    public function resolveThreadUri(string $postSlug, string $commentGuid): string
    {
        return sprintf('%s#comment-%s', $this->resolvePostUriBySlug($postSlug), $commentGuid);
    }
}
