<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use App\FunctionalTestCase;
use App\Service\DateTime\DateTimeFactory;
use DateTimeImmutable;

class PostRepositoryTest extends FunctionalTestCase
{
    private PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostRepository($this->getDoctrineRegistry());

        $this->createFixtures();
    }

    protected function tearDown(): void
    {
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

    /** @return iterable<string[]> */
    public function postSlugProvider(): iterable
    {
        yield ['Some-slug-1'];
        yield ['Some-slug-2'];
        yield ['Some-slug-3'];
        yield ['Some-slug-4'];
        yield ['Some-slug-5'];
        yield ['Another-slug-1'];
        yield ['Another-slug-2'];
        yield ['Another-slug-3'];
        yield ['Another-slug-4'];
        yield ['Multitagged-slug'];
    }

    public function testSave(): void
    {
        $slug = 'Some-new-post-slug';
        self::assertNull($this->repository->findOneBySlug($slug));

        $post = new Post($slug, 'Some new post', 'Some preview', 'Some content');
        $this->repository->save($post);

        $savedPost = $this->repository->findOneBySlug($slug);

        self::assertSame($savedPost, $post);
    }

    /**
     * @param string   $tag
     * @param int      $limit
     * @param int      $offset
     * @param string[] $expectedTitles
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

    /** @return iterable<array{string, int, int, string[]}> */
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
     * @param int      $limit
     * @param int      $offset
     * @param string[] $expectedTitles
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

    /** @return iterable<array{int, int, string[]}> */
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
        $entityManager = $this->getEntityManager();

        $someTag    = new Tag('SomeTag');
        $anotherTag = new Tag('AnotherTag');

        // An artificial time gap between posts with step >=1second
        $counter = -60;
        foreach (range(1, 5) as $postWithSomeTag) {
            DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('%d seconds', ++$counter)));
            $post = new Post(
                'Some-slug-' . $postWithSomeTag,
                'Some title ' . $postWithSomeTag,
                'Some preview ' . $postWithSomeTag,
                'Some content ' . $postWithSomeTag
            );
            $post->addTag($someTag);
            $entityManager->persist($post);
        }

        foreach (range(1, 4) as $postWithAnotherTag) {
            DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
            $post2 = new Post(
                'Another-slug-' . $postWithAnotherTag,
                'Another title ' . $postWithAnotherTag,
                'Another preview ' . $postWithAnotherTag,
                'Another content ' . $postWithAnotherTag
            );
            $post2->addTag($anotherTag);
            $entityManager->persist($post2);
        }

        DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
        $postWithMultipleTags = new Post(
            'Multitagged-slug',
            'Multitagged post',
            'Some multitag preview',
            'Some multitag content'
        );
        $postWithMultipleTags->addTag($someTag);
        $postWithMultipleTags->addTag($anotherTag);
        $entityManager->persist($postWithMultipleTags);

        $entityManager->flush();
    }
}
