<?php

declare(strict_types=1);

namespace Frontend\Controller;

use App\Lib\Response\Cache\CacheGatewayInterface;
use App\Lib\Response\Cache\TTL;
use Frontend\Service\SitemapGenerator;
use Symfony\Component\HttpFoundation\Response;

readonly class SitemapController
{
    public function __construct(
        private SitemapGenerator $sitemapGenerator,
        private CacheGatewayInterface $cacheGateway
    ) {
    }

    public function __invoke(): Response
    {
        $content  = $this->sitemapGenerator->generate();
        $response = new Response($content, 200, ['Content-Type' => 'application/xml;charset=UTF-8']);

        return $this->cacheGateway->cache($response, TTL::hours(24));
    }
}
