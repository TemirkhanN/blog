<?php

declare(strict_types=1);

namespace App\Lib\Response\Cache;

use Symfony\Component\HttpFoundation\Response;

class CacheGateway implements CacheGatewayInterface
{
    public function cache(Response $response, TTL $ttl): Response
    {
        if ($ttl->toSeconds() === 0) {
            return $response;
        }

        $response->setPublic();
        $response->setMaxAge($ttl->toSeconds());

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
