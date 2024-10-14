<?php

declare(strict_types=1);

namespace App\Controller;

use App\FunctionalTestCase;
use Symfony\Component\HttpKernel\CacheClearer\Psr6CacheClearer;

class IssueTokenControllerTest extends FunctionalTestCase
{
    private const TRACKER_STORAGE = 'cache.rate_limiter';

    private const RATE_LIMIT = 5;

    private const API_URI = '/api/auth/tokens';

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetLimits();
    }

    protected function tearDown(): void
    {
        $this->resetLimits();

        parent::tearDown();
    }

    public function testIssueTokenOverRateLimit(): void
    {
        for ($i = 0; $i < self::RATE_LIMIT; $i++) {
            $response = $this->sendRequest('POST', self::API_URI);
            // Assuming, api works normally for each request
            self::assertEquals('{"code":400,"message":"Login or password can not be empty"}', $response->getContent());
        }

        // Once rate limit is exhausted, a different response must be applied
        $response = $this->sendRequest('POST', self::API_URI);
        self::assertEquals('{"code":403,"message":"Too many requests"}', $response->getContent());
    }

    public function testIssueTokenSuccess(): void
    {
        // Once rate limit is exhausted, a different response must be applied
        $response = $this->sendRequest('POST', self::API_URI, ['login' => 'admin', 'password' => 'SomeHardCodedToken']);

        $raw = (string) $response->getContent();
        self::assertJson($raw);
        $data = (array) json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('token', $data);
    }

    private function resetLimits(): void
    {
        /** @var Psr6CacheClearer $cacheClearer */
        // @phpstan-ignore-next-line
        $cacheClearer = $this->getService('cache.global_clearer');

        $cacheClearer->clearPool(self::TRACKER_STORAGE);
    }
}
