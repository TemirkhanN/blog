<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Repository\PostFilter;
use App\Domain\Repository\PostRepositoryInterface;
use SimpleXMLElement;

/**
 * @TODO does not belong to src. Move to frontend directory. Same applies to usages.
 */
readonly class SitemapGenerator
{
    private const FREQ_MONTHLY    = 'monthly';
    private const FREQ_YEARLY     = 'yearly';
    private const PRIORITY_TOP    = '1.0';
    private const PRIORITY_NORMAL = '0.5';

    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UriResolver $uriResolver
    ) {
    }

    public function generate(): string
    {
        $filter                = new PostFilter();
        $filter->onlyPublished = true;

        $doc = new SimpleXMLElement(
            '<?xml version="1.0"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
        );

        foreach ($this->postRepository->getPosts($filter) as $post) {
            $lastMod = $post->updatedAt() ?? $post->createdAt();

            $url = $doc->addChild('url');
            $url->addChild('loc', $this->uriResolver->resolvePostUri($post->id(), $post->slug()));
            $url->addChild('lastmod', $lastMod->format(DATE_ATOM));
            $url->addChild('changefreq', self::FREQ_YEARLY);
            $url->addChild('priority', self::PRIORITY_TOP);
        }

        return $doc->asXML() ? : '';
    }
}
