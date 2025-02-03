<?php

declare(strict_types=1);

namespace App;

use App\Domain\Entity\Post;
use App\Domain\Repository\PostRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FunctionalTestCase extends WebTestCase
{
    protected CarbonImmutable $currentTime;
    private CarbonImmutable $absoluteCurrentTime;

    private ?string $authToken = null;

    private KernelBrowser $browser;

    /** @var array<ClassMetadata<object>> */
    private static array $cachedMetadata = [];

    private ManagerRegistry $doctrineRegistry;

    private PostRepositoryInterface $postRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->absoluteCurrentTime = new CarbonImmutable('2023-12-27 13:00:00');
        $this->currentTime         = $this->absoluteCurrentTime;

        $this->setCurrentTime($this->currentTime);

        $this->browser = self::createClient();

        /** @var ManagerRegistry $doctrineRegistry */
        // @phpstan-ignore-next-line
        $doctrineRegistry       = self::$kernel->getContainer()->get('doctrine');
        $this->doctrineRegistry = $doctrineRegistry;

        $schema = new SchemaTool($this->getEntityManager());

        if (self::$cachedMetadata === []) {
            self::$cachedMetadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
        }

        $schema->createSchema(static::$cachedMetadata);

        $this->postRepository = $this->getService(PostRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        $schema = new SchemaTool($this->getEntityManager());
        $schema->dropSchema(static::$cachedMetadata);

        $this->authToken = null;
        $this->setCurrentTime(null);

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
        $this->authToken = (string) password_hash($token, PASSWORD_BCRYPT);
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
    final protected static function assertJsonEqualsToData(string $actualJson, mixed $expectedData): void
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

        $data = (array) json_decode($content, true);

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

    protected function refreshState(object $entity): void
    {
        $this->getEntityManager()->refresh($entity);
    }

    /**
     * @param string       $message
     * @param array<mixed> $details
     * @param int          $code
     *
     * @return void
     */
    protected function assertResponseContainsError(string $message, array $details = [], int $code = 0): void
    {
        $response = $this->browser->getResponse();

        self::assertJsonEqualsToData(
            (string) $response->getContent(),
            [
                'message' => $message,
                'code'    => $code,
                'details' => $details,
            ]
        );
    }

    protected function setCurrentTime(?CarbonImmutable $currentTime): void
    {
        if ($currentTime === null) {
            $currentTime = $this->absoluteCurrentTime;
        }

        $this->currentTime = $currentTime;
        Carbon::setTestNow($currentTime);
        CarbonImmutable::setTestNow($currentTime);
    }


    /**
     * @param string        $title
     * @param string        $preview
     * @param string        $content
     * @param array<string> $tags
     *
     * @return Post
     */
    protected function createPost(
        string $title,
        string $preview,
        string $content,
        array $tags = []
    ): Post {
        $post = new Post($title, $preview, $content);
        $post->setTags($tags);
        $this->postRepository->save($post);

        return $post;
    }

    protected function saveState(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    protected function getService(string $className): object
    {
        // @phpstan-ignore-next-line
        return $this->getContainer()->get($className);
    }
}
