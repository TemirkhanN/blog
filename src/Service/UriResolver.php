<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UriResolver
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function resolvePostUri(string $postSlug): string
    {
        return $this->urlGenerator->generate(
            'blog_post',
            ['slug' => $postSlug],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function resolveThreadUri(string $postSlug, string $commentGuid): string
    {
        return sprintf('%s#comment-%s', $this->resolvePostUri($postSlug), $commentGuid);
    }

    public function resolveTaggedPostsUri(string $tag): string
    {
        return $this->urlGenerator->generate(
            'blog_posts_by_tag',
            ['tag' => $tag],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
