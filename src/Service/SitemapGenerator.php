<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Repository\PostFilter;
use App\Domain\Repository\PostRepositoryInterface;
use SimpleXMLElement;

class SitemapGenerator
{
    private const FREQ_MONTHLY    = 'monthly';
    private const FREQ_YEARLY     = 'yearly';
    private const PRIORITY_TOP    = '1.0';
    private const PRIORITY_NORMAL = '0.5';

    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly UriResolver $uriResolver
    ) {
    }

    public function generate(): string
    {
        $filter                = new PostFilter();
        $filter->onlyPublished = true;

        $doc = new SimpleXMLElement(
            '<?xml version="1.0"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
        );

        $tags = [];
        foreach ($this->postRepository->getPosts($filter) as $post) {
            $lastMod = $post->updatedAt() ?? $post->createdAt();
            foreach ($post->tags() as $tag) {
                $tags[$tag] = true;
            }

            $url = $doc->addChild('url');
            $url->addChild('loc', $this->uriResolver->resolvePostUri($post->slug()));
            $url->addChild('lastmod', $lastMod->format(DATE_ATOM));
            $url->addChild('changefreq', self::FREQ_YEARLY);
            $url->addChild('priority', self::PRIORITY_TOP);
        }

        foreach (array_keys($tags) as $tag) {
            $url = $doc->addChild('url');
            $url->addChild('loc', $this->uriResolver->resolveTaggedPostsUri($tag));
            $url->addChild('changefreq', self::FREQ_MONTHLY);
            $url->addChild('priority', self::PRIORITY_NORMAL);
        }

        return $doc->asXML() ? : '';
    }
}
