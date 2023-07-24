<?php
declare(strict_types=1);

namespace Frontend\Controller;

use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use Symfony\Component\HttpFoundation\Response;

class SitemapController
{
    public function __construct(
        private readonly CacheGatewayInterface $cacheGateway
    ) {}

    public function __invoke(): Response
    {
        $sitemapFile = __DIR__ . '/../../public/sitemap.xml';
        if (!file_exists($sitemapFile)) {
            return new Response('', 404);
        }

        $content = file_get_contents($sitemapFile);
        $response = new Response($content, 200, ['Content-Type' => 'application/xml;charset=UTF-8']);

        return $this->cacheGateway->cache($response, TTL::hours(24));
    }
}
