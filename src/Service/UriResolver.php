<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class UriResolver
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function resolvePostUri(int $id, string $postSlug): string
    {
        return $this->urlGenerator->generate(
            'blog_post',
            [
            'id'   => $id,
            'slug' => $postSlug,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function resolveThreadUri(int $id, string $postSlug, string $commentGuid): string
    {
        return sprintf('%s#comment-%s', $this->resolvePostUri($id, $postSlug), $commentGuid);
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
