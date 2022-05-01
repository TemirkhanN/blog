<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use App\FunctionalTestCase;
use App\Service\DateTime\DateTimeFactory;
use App\Service\Post\Dto\PostFilter;
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
        $emptyFilter             = new PostFilter();
        $filterBySomeTag         = new PostFilter();
        $filterBySomeTag->tag    = 'SomeTag';
        $filterByAnotherTag      = new PostFilter();
        $filterByAnotherTag->tag = 'AnotherTag';

        self::assertEquals(10, $this->repository->countPosts($emptyFilter));
        self::assertEquals(6, $this->repository->countPosts($filterBySomeTag));
        self::assertEquals(5, $this->repository->countPosts($filterByAnotherTag));
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
        self::assertEquals($slug, $post->slug());
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
     * @param PostFilter $filter
     * @param string[]   $expectedTitles
     *
     * @dataProvider taggedPostsProvider
     */
    public function testFindPostsByTag(PostFilter $filter, array $expectedTitles): void
    {
        $posts = $this->repository->getPosts($filter);

        $existingTitles = [];
        foreach ($posts as $post) {
            $existingTitles[] = $post->title();
        }

        self::assertSame($expectedTitles, $existingTitles);
    }

    /** @return iterable<array{PostFilter, string[]}> */
    public function taggedPostsProvider(): iterable
    {
        yield 'Latest 2 posts tagged with SomeTag' => [
            PostFilter::create(2, 0, 'SomeTag'),
            [
                'Multitagged post',
                'Some title 5',
            ],
        ];

        yield 'Latest 3rd, 4th and 5th posts tagged with SomeTag' => [
            PostFilter::create(3, 2, 'SomeTag'),
            [
                'Some title 4',
                'Some title 3',
                'Some title 2',
            ],
        ];

        yield 'Latest 5 posts tagged with AnotherTag skipping 3 entries' => [
            PostFilter::create(5, 3, 'AnotherTag'),
            [
                'Another title 2',
                'Another title 1',
            ],
        ];

        yield 'Latest post tagged with AnotherTag skipping 100 entries' => [
            PostFilter::create(1, 100, 'AnotherTag'),
            [],
        ];

        yield 'Latest 5 posts tagged with SomeTag including non-published' => [
            PostFilter::create(5, 0, 'SomeTag', false),
            [
                'Archived title 24',
                'Some draft title 23',
                'Multitagged post',
                'Some title 5',
                'Some title 4',
            ],
        ];
    }

    /**
     * @param PostFilter $filter
     * @param string[]   $expectedTitles
     *
     * @dataProvider postsProvider
     */
    public function testGetPosts(PostFilter $filter, array $expectedTitles): void
    {
        $posts = $this->repository->getPosts($filter);

        $existingTitles = [];
        foreach ($posts as $post) {
            $existingTitles[] = $post->title();
        }

        self::assertSame($expectedTitles, $existingTitles);
    }

    /** @return iterable<array{PostFilter, string[]}> */
    public function postsProvider(): iterable
    {
        yield 'Latest 2 posts' => [
            PostFilter::create(2, 0),
            [
                'Multitagged post',
                'Another title 4',
            ],
        ];

        yield 'Latest 3 posts skipping 2 entries' => [
            PostFilter::create(3, 2),
            [
                'Another title 3',
                'Another title 2',
                'Another title 1',
            ],
        ];

        yield 'Latest 5 posts skipping 3 entries' => [
            PostFilter::create(5, 3),
            [
                'Another title 2',
                'Another title 1',
                'Some title 5',
                'Some title 4',
                'Some title 3',
            ],
        ];

        yield 'Latest post skipping 100 entries' => [
            PostFilter::create(1, 100),
            [],
        ];

        yield 'Latest 5 posts including non-published' => [
            PostFilter::create(5, 0, null, false),
            [
                'Some draft title 25',
                'Archived title 24',
                'Some draft title 23',
                'Multitagged post',
                'Another title 4',
            ],
        ];
    }

    private function createFixtures(): void
    {
        $entityManager = $this->getEntityManager();

        $someTag    = new Tag('SomeTag');
        $anotherTag = new Tag('AnotherTag');

        // An artificial time gap between posts with step >=1second
        $counter = 60;
        foreach (range(1, 5) as $postWithSomeTag) {
            DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
            $post = new Post(
                'Some-slug-' . $postWithSomeTag,
                'Some title ' . $postWithSomeTag,
                'Some preview ' . $postWithSomeTag,
                'Some content ' . $postWithSomeTag
            );
            $post->addTag($someTag);
            $post->publish();
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
            $post2->publish();
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
        $postWithMultipleTags->publish();
        $entityManager->persist($postWithMultipleTags);

        DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
        $draftPost = new Post('23th-post-slug', 'Some draft title 23', 'Some preview of 23', 'Some content of 23');
        $draftPost->setTags($someTag, $anotherTag);
        DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
        $archivedPost = new Post('24th-post-slug', 'Archived title 24', 'Some preview of 24', 'Some content of 24');
        $archivedPost->setTags($someTag, $anotherTag);
        $archivedPost->archive();

        DateTimeFactory::alwaysReturn(new DateTimeImmutable(sprintf('-%d seconds', --$counter)));
        $draftPostThatWasNeverUpdated = new Post(
            '25th-post-slug',
            'Some draft title 25',
            'Some preview of 25',
            'Some content of 25'
        );

        $entityManager->persist($draftPost);
        $entityManager->persist($archivedPost);
        $entityManager->persist($draftPostThatWasNeverUpdated);

        $entityManager->flush();
    }
}
