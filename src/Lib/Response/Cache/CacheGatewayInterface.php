<?php

declare(strict_types=1);

namespace App\Lib\Response\Cache;

use Symfony\Component\HttpFoundation\Response;

interface CacheGatewayInterface
{
    public function cache(Response $response, TTL $ttl): Response;
}
