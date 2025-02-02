<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Entity\Post;
use App\Domain\Repository\PostRepositoryInterface;
use App\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class ListControllerTest extends FunctionalTestCase
{
    private const API_URL = '/api/posts';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createPosts();
    }

    /**
     * @param array<string, int|string> $query
     * @param string                    $error
     *
     * @dataProvider badRequestProvider
     */
    public function testBadRequest(array $query, string $error): void
    {
        $response = $this->sendRequest('GET', self::API_URL . '?' . http_build_query($query));

        self::assertEquals($error, $response->getContent());
    }

    /**
     * @return iterable<array{
     *     query: array{limit?: int, offset?: int},
     *     error: string
     * }>
     */
    public function badRequestProvider(): iterable
    {
        yield 'Invalid offset' => [
            'query' => ['offset' => -1],
            'error' => '{"code":400,"message":"Offset can not be less than 0"}',
        ];

        yield 'Invalid limit' => [
            'query' => ['limit' => 0],
            'error' => '{"code":400,"message":"Limit can not be less than 1 or too high"}',
        ];

        yield 'Too high limit' => [
            'query' => ['limit' => 21],
            'error' => '{"code":400,"message":"Limit can not be less than 1 or too high"}',
        ];
    }

    /**
     * @param array<string, int|string>                  $query
     * @param string[]                                   $matchingPosts
     * @param array{limit: int, offset: int, total: int} $pagination
     *
     * @dataProvider postFilterProvider
     */
    public function testListPosts(array $query, array $matchingPosts, array $pagination): void
    {
        $response = $this->sendRequest('GET', self::API_URL . '?' . http_build_query($query));

        self::assertEquals(200, $response->getStatusCode());

        self::assertResponseContainsPagination(
            $response,
            $pagination['limit'],
            $pagination['offset'],
            $pagination['total']
        );

        $this->assertResponseContainsPosts($matchingPosts, $response);
    }

    /**
     * @return iterable<array{
     *     query: array{limit?: int, offset?: int, tag?: string},
     *     matchingPosts: string[],
     *     pagination: array{limit: int, offset: int, total: int}
     * }>
     */
    public function postFilterProvider(): iterable
    {
        yield 'Last 10 posts' => [
            'query'         => [],
            'matchingPosts' => [
                '2023-12-27_Some-title-22',
                '2023-12-27_Some-title-21',
                '2023-12-27_Some-title-20',
                '2023-12-27_Some-title-19',
                '2023-12-27_Some-title-18',
                '2023-12-27_Some-title-17',
                '2023-12-27_Some-title-16',
                '2023-12-27_Some-title-15',
                '2023-12-27_Some-title-14',
                '2023-12-27_Some-title-13',
            ],
            'pagination'    => [
                'limit'  => 10,
                'offset' => 0,
                'total'  => 22,
            ],
        ];

        yield 'Last 5 posts' => [
            'query'         => ['limit' => 5],
            'matchingPosts' => [
                '2023-12-27_Some-title-22',
                '2023-12-27_Some-title-21',
                '2023-12-27_Some-title-20',
                '2023-12-27_Some-title-19',
                '2023-12-27_Some-title-18',
            ],
            'pagination'    => [
                'limit'  => 5,
                'offset' => 0,
                'total'  => 22,
            ],
        ];

        yield 'Last 5 posts from 4th position' => [
            'query'         => [
                'limit'  => 5,
                'offset' => 4,
            ],
            'matchingPosts' => [
                '2023-12-27_Some-title-18',
                '2023-12-27_Some-title-17',
                '2023-12-27_Some-title-16',
                '2023-12-27_Some-title-15',
                '2023-12-27_Some-title-14',
            ],
            'pagination'    => [
                'limit'  => 5,
                'offset' => 4,
                'total'  => 22,
            ],
        ];

        yield 'Maximum allowed limit' => [
            'query'         => ['limit' => 20],
            'matchingPosts' => [
                '2023-12-27_Some-title-22',
                '2023-12-27_Some-title-21',
                '2023-12-27_Some-title-20',
                '2023-12-27_Some-title-19',
                '2023-12-27_Some-title-18',
                '2023-12-27_Some-title-17',
                '2023-12-27_Some-title-16',
                '2023-12-27_Some-title-15',
                '2023-12-27_Some-title-14',
                '2023-12-27_Some-title-13',
                '2023-12-27_Some-title-12',
                '2023-12-27_Some-title-11',
                '2023-12-27_Some-title-10',
                '2023-12-27_Some-title-9',
                '2023-12-27_Some-title-8',
                '2023-12-27_Some-title-7',
                '2023-12-27_Some-title-6',
                '2023-12-27_Some-title-5',
                '2023-12-27_Some-title-4',
                '2023-12-27_Some-title-3',
            ],
            'pagination'    => [
                'limit'  => 20,
                'offset' => 0,
                'total'  => 22,
            ],
        ];

        yield 'Offset and limit over total amount' => [
            'query'         => [
                'limit'  => 5,
                'offset' => 20,
            ],
            'matchingPosts' => [
                '2023-12-27_Some-title-2',
                '2023-12-27_Some-title-1',
            ],
            'pagination'    => [
                'limit'  => 5,
                'offset' => 20,
                'total'  => 22,
            ],
        ];

        yield 'Posts tagged with "SomeTag"' => [
            'query'         => ['tag' => 'SomeTag'],
            'matchingPosts' => [
                '2023-12-27_Some-title-14',
                '2023-12-27_Some-title-13',
                '2023-12-27_Some-title-12',
                '2023-12-27_Some-title-10',
                '2023-12-27_Some-title-7',
                '2023-12-27_Some-title-1',
            ],
            'pagination'    => [
                'limit'  => 10,
                'offset' => 0,
                'total'  => 6,
            ],
        ];

        yield 'Posts tagged with "AnotherTag"' => [
            'query'         => ['tag' => 'AnotherTag'],
            'matchingPosts' => [
                '2023-12-27_Some-title-13',
                '2023-12-27_Some-title-11',
                '2023-12-27_Some-title-10',
                '2023-12-27_Some-title-6',
                '2023-12-27_Some-title-2',
            ],
            'pagination'    => [
                'limit'  => 10,
                'offset' => 0,
                'total'  => 5,
            ],
        ];

        yield 'Posts tagged with "OneMoreTag"' => [
            'query'         => ['tag' => 'OneMoreTag'],
            'matchingPosts' => [
                '2023-12-27_Some-title-22',
                '2023-12-27_Some-title-15',
                '2023-12-27_Some-title-14',
                '2023-12-27_Some-title-13',
                '2023-12-27_Some-title-12',
                '2023-12-27_Some-title-11',
                '2023-12-27_Some-title-5',
                '2023-12-27_Some-title-3',
            ],
            'pagination'    => [
                'limit'  => 10,
                'offset' => 0,
                'total'  => 8,
            ],
        ];

        yield 'Posts tagged with "OneMoreTag" with limit and offset' => [
            'query'         => [
                'tag'    => 'OneMoreTag',
                'limit'  => 4,
                'offset' => 3,
            ],
            'matchingPosts' => [
                '2023-12-27_Some-title-13',
                '2023-12-27_Some-title-12',
                '2023-12-27_Some-title-11',
                '2023-12-27_Some-title-5',
            ],
            'pagination'    => [
                'limit'  => 4,
                'offset' => 3,
                'total'  => 8,
            ],
        ];
    }

    private function createPosts(): void
    {
        $postRepository = $this->getService(PostRepositoryInterface::class);

        $posts = [
            [
                'tags' => ['SomeTag'],
            ],
            [
                'tags' => ['AnotherTag'],
            ],
            [
                'tags' => ['OneMoreTag'],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => ['OneMoreTag'],
            ],
            [
                'tags' => ['AnotherTag'],
            ],
            [
                'tags' => ['SomeTag'],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => [
                    'SomeTag',
                    'AnotherTag',
                ],
            ],
            [
                'tags' => [
                    'AnotherTag',
                    'OneMoreTag',
                ],
            ],
            [
                'tags' => [
                    'OneMoreTag',
                    'SomeTag',
                ],
            ],
            [
                'tags' => [
                    'SomeTag',
                    'AnotherTag',
                    'OneMoreTag',
                ],
            ],
            [
                'tags' => [
                    'SomeTag',
                    'OneMoreTag',
                ],
            ],
            [
                'tags' => ['OneMoreTag'],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => [],
            ],
            [
                'tags' => ['OneMoreTag'],
            ],
        ];

        $this->currentTime = $this->currentTime->subSeconds(count($posts) + 1);
        foreach ($posts as $key => $postDetails) {
            $this->currentTime = $this->currentTime->addSecond();
            $this->setCurrentTime($this->currentTime);

            $order = $key + 1;

            $post = new Post(
                $postRepository,
                'Some title ' . $order,
                'Some preview ' . $order,
                'Some content ' . $order
            );
            $post->setTags($postDetails['tags']);
            $post->publish();

            $postRepository->save($post);
        }

        $draftPost = new Post($postRepository, 'Some title 23', 'Some preview of 23', 'Some content of 23');
        $draftPost->setTags(['SomeTag', 'AnotherTag', 'OneMoreTag']);
        $archivedPost = new Post($postRepository, 'Some title 24', 'Some preview of 24', 'Some content of 24');
        $archivedPost->setTags(['SomeTag', 'AnotherTag', 'OneMoreTag']);
        $archivedPost->archive();

        $postRepository->save($draftPost);
        $postRepository->save($archivedPost);
    }

    /**
     * @param array<string> $postsSlugs
     * @param Response      $response
     */
    private function assertResponseContainsPosts(array $postsSlugs, Response $response): void
    {
        $content = (string) $response->getContent();
        self::assertJson($content);
        $responseData = (array) json_decode($content, true);

        self::assertArrayHasKey('data', $responseData);

        self::assertEquals(count($postsSlugs), count($responseData['data']), 'Posts amount mismatch');

        $repository = $this->getEntityManager()->getRepository(Post::class);

        $posts = [];
        foreach ($postsSlugs as $slug) {
            /** @var Post $post */
            $post = $repository->findOneBy(['slug' => $slug]);

            self::assertNotNull($post);
            self::assertNotNull($post->publishedAt());
            self::assertNotNull($post->updatedAt());

            $posts[] = [
                'slug'        => $post->slug(),
                'title'       => $post->title(),
                'publishedAt' => $post->publishedAt()->format(DATE_W3C),
                'createdAt'   => $post->createdAt()->format(DATE_W3C),
                'updatedAt'   => $post->updatedAt()->format(DATE_W3C),
                'preview'     => $post->preview(),
                'tags'        => $post->tags(),
            ];
        }

        self::assertEquals($posts, $responseData['data']);
    }
}
