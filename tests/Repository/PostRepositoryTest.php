<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use App\Service\DateTime\DateTimeFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostRepositoryTest extends KernelTestCase
{
    /**
     * @var array<ClassMetadata>
     */
    private static array $cachedMetadata = [];

    private EntityManager $entityManager;

    private PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        /* @var ManagerRegistry $doctrine */
        $doctrine = self::$kernel->getContainer()->get('doctrine');
        /* @var EntityManager $defaultEm */
        $defaultEm           = $doctrine->getManager();
        $this->entityManager = $defaultEm;
        $schema              = new SchemaTool($this->entityManager);
        // TODO move to abstract repository testcase(cache with static) for further memory/time consumption mitigation
        if (static::$cachedMetadata === []) {
            static::$cachedMetadata = $defaultEm->getMetadataFactory()->getAllMetadata();
        }

        $schema->createSchema(static::$cachedMetadata);

        $this->repository = new PostRepository($doctrine);

        $this->createFixtures();
    }

    protected function tearDown(): void
    {
        $schema = new SchemaTool($this->entityManager);
        $schema->dropSchema(static::$cachedMetadata);
        DateTimeFactory::alwaysReturn(null);

        parent::tearDown();
    }

    public function testCountPosts(): void
    {
        self::assertEquals(10, $this->repository->countPosts());
    }

    public function testCountPostsByTag(): void
    {
        self::assertEquals(6, $this->repository->countPostsByTag('SomeTag'));
        self::assertEquals(5, $this->repository->countPostsByTag('AnotherTag'));
    }

    /**
     * @param string $slug
     *
     * @dataProvider postSlugProvider
     */
    public function testFindOneBuSlug(string $slug): void
    {
        $post = $this->repository->findOneBySlug($slug);

        self::assertNotNull($post);
        self::assertEquals($slug, $post->getSlug());
    }

    public function postSlugProvider(): iterable
    {
        $today = date('Y-m-d');

        yield [$today . '_Some-title-1'];
        yield [$today . '_Some-title-2'];
        yield [$today . '_Some-title-3'];
        yield [$today . '_Some-title-4'];
        yield [$today . '_Some-title-5'];
        yield [$today . '_Another-title-1'];
        yield [$today . '_Another-title-2'];
        yield [$today . '_Another-title-3'];
        yield [$today . '_Another-title-4'];
        yield [$today . '_Multitagged-post'];
    }

    public function testSave(): void
    {
        $post = new Post('Some new post', 'Some preview', 'Some content');
        $slug = $post->getSlug();

        self::assertNull($this->repository->findOneBySlug($slug));

        $this->repository->save($post);

        $savedPost = $this->repository->findOneBySlug($slug);

        self::assertSame($savedPost, $post);
    }

    /**
     * @param string $tag
     * @param int    $limit
     * @param int    $offset
     * @param array  $expectedTitles
     *
     * @dataProvider taggedPostsProvider
     */
    public function testFindPostsByTag(string $tag, int $limit, int $offset, array $expectedTitles): void
    {
        $posts = $this->repository->findPostsByTag($tag, $limit, $offset);

        $existingTitles = [];
        foreach ($posts as $post) {
            $existingTitles[] = $post->getTitle();
        }

        self::assertSame($expectedTitles, $existingTitles);
    }

    public function taggedPostsProvider(): iterable
    {
        // Last 2
        yield [
            'SomeTag',
            2,
            0,
            [
                'Multitagged post',
                'Some title 5',
            ],
        ];

        yield [
            'SomeTag',
            3,
            2,
            [
                'Some title 4',
                'Some title 3',
                'Some title 2',
            ],
        ];

        // Overflowing limit
        yield [
            'AnotherTag',
            5,
            3,
            [
                'Another title 2',
                'Another title 1',
            ],
        ];

        // Overflowing offset
        yield [
            'AnotherTag',
            1,
            100,
            [],
        ];
    }

    /**
     * @param int   $limit
     * @param int   $offset
     * @param array $expectedTitles
     *
     * @dataProvider postsProvider
     */
    public function testGetPosts(int $limit, int $offset, array $expectedTitles): void
    {
        $posts = $this->repository->getPosts($limit, $offset);

        $existingTitles = [];
        foreach ($posts as $post) {
            $existingTitles[] = $post->getTitle();
        }

        self::assertSame($expectedTitles, $existingTitles);
    }

    public function postsProvider(): iterable
    {
        yield [
            2,
            0,
            [
                'Multitagged post',
                'Another title 4',
            ],
        ];

        yield [
            3,
            2,
            [
                'Another title 3',
                'Another title 2',
                'Another title 1',
            ],
        ];

        // Limit overflow
        yield [
            5,
            3,
            [
                'Another title 2',
                'Another title 1',
                'Some title 5',
                'Some title 4',
                'Some title 3',
            ],
        ];

        // Offset overflow
        yield [
            1,
            100,
            [],
        ];
    }

    private function createFixtures(): void
    {
        $someTag    = new Tag('SomeTag');
        $anotherTag = new Tag('AnotherTag');

        $counter = 60;
        foreach (range(1, 5) as $postWithSomeTag) {
            DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
            $post = new Post(
                'Some title ' . $postWithSomeTag,
                'Some preview ' . $postWithSomeTag,
                'Some content ' . $postWithSomeTag
            );
            $post->addTag($someTag);
            $this->entityManager->persist($post);
        }

        foreach (range(1, 4) as $postWithAnotherTag) {
            DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
            $post2 = new Post(
                'Another title ' . $postWithAnotherTag,
                'Another preview ' . $postWithAnotherTag,
                'Another content ' . $postWithAnotherTag
            );
            $post2->addTag($anotherTag);
            $this->entityManager->persist($post2);
        }

        DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
        $postWithMultipleTags = new Post(
            'Multitagged post',
            'Some multitag preview',
            'Some multitag content'
        );
        $postWithMultipleTags->addTag($someTag);
        $postWithMultipleTags->addTag($anotherTag);
        $this->entityManager->persist($postWithMultipleTags);

        $this->entityManager->flush();
    }
}
