<?php

declare(strict_types=1);

namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FunctionalTestCase extends WebTestCase
{
    private ?string $authToken = null;

    private KernelBrowser $browser;

    /** @var array<ClassMetadata<object>> */
    private static array $cachedMetadata = [];

    private ManagerRegistry $doctrineRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->browser = self::createClient();

        /** @var ManagerRegistry $doctrineRegistry */
        $doctrineRegistry       = self::$kernel->getContainer()->get('doctrine');
        $this->doctrineRegistry = $doctrineRegistry;

        $schema = new SchemaTool($this->getEntityManager());

        if (self::$cachedMetadata === []) {
            self::$cachedMetadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
        }

        $schema->createSchema(static::$cachedMetadata);
    }

    protected function tearDown(): void
    {
        $schema = new SchemaTool($this->getEntityManager());
        $schema->dropSchema(static::$cachedMetadata);

        $this->authToken = null;

        parent::tearDown();
    }

    final protected function getDoctrineRegistry(): ManagerRegistry
    {
        return $this->doctrineRegistry;
    }

    final protected function getEntityManager(): EntityManager
    {
        /** @var EntityManager $manager */
        $manager = $this->doctrineRegistry->getManager();

        return $manager;
    }

    final protected function authenticate(string $token): void
    {
        $this->authToken = $token;
    }

    /**
     * @param string               $method
     * @param string               $uri
     * @param array<string, mixed> $parameters
     *
     * @return Response
     */
    final protected function sendRequest(string $method, string $uri, array $parameters = []): Response
    {
        $server = [];
        if ($this->authToken !== null) {
            $server['HTTP_Authorization'] = $this->authToken;
        }

        $this->browser->jsonRequest($method, $uri, $parameters, $server);

        return $this->browser->getResponse();
    }

    /**
     * @param string $actualJson
     * @param mixed  $expectedData
     */
    final protected static function assertJsonEqualsToData(string $actualJson, $expectedData): void
    {
        self::assertJson($actualJson);

        $actualData = json_decode($actualJson, true);

        self::assertEquals($expectedData, $actualData);
    }

    final protected static function assertResponseContainsPagination(
        Response $response,
        int $limit,
        int $offset,
        int $totalItems
    ): void {
        $content = (string) $response->getContent();

        self::assertJson($content);

        $data = json_decode($content, true);

        self::assertArrayHasKey('pagination', $data);
        self::assertEquals(
            [
                'limit'  => $limit,
                'offset' => $offset,
                'total'  => $totalItems,
            ],
            $data['pagination']
        );
    }
}
