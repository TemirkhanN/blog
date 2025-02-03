<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Entity\Post;
use App\Domain\Repository\PostFilter;
use App\FunctionalTestCase;

class PostRepositoryTest extends FunctionalTestCase
{
    private PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getService(PostRepository::class);

        $this->createFixtures();
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
        yield ['2023-12-27_Some-title-1'];
        yield ['2023-12-27_Some-title-2'];
        yield ['2023-12-27_Some-title-3'];
        yield ['2023-12-27_Some-title-4'];
        yield ['2023-12-27_Some-title-5'];
        yield ['2023-12-27_Another-title-1'];
        yield ['2023-12-27_Another-title-2'];
        yield ['2023-12-27_Another-title-3'];
        yield ['2023-12-27_Another-title-4'];
        yield ['2023-12-27_Multitagged-post'];
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
        // An artificial time gap between posts with step >=1second
        $this->setCurrentTime($this->currentTime->subSeconds(60));

        foreach (range(1, 5) as $postWithSomeTag) {
            $this->setCurrentTime($this->currentTime->addSecond());

            $post = new Post(
                'Some title ' . $postWithSomeTag,
                'Some preview ' . $postWithSomeTag,
                'Some content ' . $postWithSomeTag
            );
            $post->setTags(['SomeTag']);
            $post->publish();
            $this->repository->save($post);
        }

        foreach (range(1, 4) as $postWithAnotherTag) {
            $this->setCurrentTime($this->currentTime->addSecond());
            $post2 = new Post(
                'Another title ' . $postWithAnotherTag,
                'Another preview ' . $postWithAnotherTag,
                'Another content ' . $postWithAnotherTag
            );
            $post2->setTags(['AnotherTag']);
            $post2->publish();
            $this->repository->save($post2);
        }

        $this->setCurrentTime($this->currentTime->addSecond());
        $postWithMultipleTags = new Post(
            'Multitagged post',
            'Some multitag preview',
            'Some multitag content'
        );
        $postWithMultipleTags->setTags(['SomeTag', 'AnotherTag']);
        $postWithMultipleTags->publish();
        $this->repository->save($postWithMultipleTags);

        $this->setCurrentTime($this->currentTime->addSecond());
        $draftPost = new Post(
            'Some draft title 23',
            'Some preview of 23',
            'Some content of 23'
        );
        $draftPost->setTags(['SomeTag', 'AnotherTag']);
        $this->repository->save($draftPost);

        $this->setCurrentTime($this->currentTime->addSecond());
        $archivedPost = new Post(
            'Archived title 24',
            'Some preview of 24',
            'Some content of 24'
        );
        $archivedPost->setTags(['SomeTag', 'AnotherTag']);
        $archivedPost->archive();
        $this->repository->save($archivedPost);

        $this->setCurrentTime($this->currentTime->addSecond());
        $draftPostThatWasNeverUpdated = new Post(
            'Some draft title 25',
            'Some preview of 25',
            'Some content of 25'
        );

        $this->repository->save($draftPostThatWasNeverUpdated);
    }
}
