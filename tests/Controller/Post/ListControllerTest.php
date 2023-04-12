<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Entity\Post;
use App\Entity\Tag;
use App\FunctionalTestCase;
use App\Service\DateTime\DateTimeFactory;
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
                '22th-post-slug',
                '21th-post-slug',
                '20th-post-slug',
                '19th-post-slug',
                '18th-post-slug',
                '17th-post-slug',
                '16th-post-slug',
                '15th-post-slug',
                '14th-post-slug',
                '13th-post-slug',
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
                '22th-post-slug',
                '21th-post-slug',
                '20th-post-slug',
                '19th-post-slug',
                '18th-post-slug',
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
                '18th-post-slug',
                '17th-post-slug',
                '16th-post-slug',
                '15th-post-slug',
                '14th-post-slug',
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
                '22th-post-slug',
                '21th-post-slug',
                '20th-post-slug',
                '19th-post-slug',
                '18th-post-slug',
                '17th-post-slug',
                '16th-post-slug',
                '15th-post-slug',
                '14th-post-slug',
                '13th-post-slug',
                '12th-post-slug',
                '11th-post-slug',
                '10th-post-slug',
                '9th-post-slug',
                '8th-post-slug',
                '7th-post-slug',
                '6th-post-slug',
                '5th-post-slug',
                '4th-post-slug',
                '3rd-post-slug',
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
                '2nd-post-slug',
                '1st-post-slug',
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
                '14th-post-slug',
                '13th-post-slug',
                '12th-post-slug',
                '10th-post-slug',
                '7th-post-slug',
                '1st-post-slug',
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
                '13th-post-slug',
                '11th-post-slug',
                '10th-post-slug',
                '6th-post-slug',
                '2nd-post-slug',
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
                '22th-post-slug',
                '15th-post-slug',
                '14th-post-slug',
                '13th-post-slug',
                '12th-post-slug',
                '11th-post-slug',
                '5th-post-slug',
                '3rd-post-slug',
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
                '13th-post-slug',
                '12th-post-slug',
                '11th-post-slug',
                '5th-post-slug',
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
        $em = $this->getEntityManager();

        $tag1 = new Tag('SomeTag');
        $tag2 = new Tag('AnotherTag');
        $tag3 = new Tag('OneMoreTag');

        $em->persist($tag1);
        $em->persist($tag2);
        $em->persist($tag3);

        $posts = [
            [
                'slug' => '1st-post-slug',
                'tags' => [$tag1],
            ],
            [
                'slug' => '2nd-post-slug',
                'tags' => [$tag2],
            ],
            [
                'slug' => '3rd-post-slug',
                'tags' => [$tag3],
            ],
            [
                'slug' => '4th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '5th-post-slug',
                'tags' => [$tag3],
            ],
            [
                'slug' => '6th-post-slug',
                'tags' => [$tag2],
            ],
            [
                'slug' => '7th-post-slug',
                'tags' => [$tag1],
            ],
            [
                'slug' => '8th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '9th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '10th-post-slug',
                'tags' => [
                    $tag1,
                    $tag2,
                ],
            ],
            [
                'slug' => '11th-post-slug',
                'tags' => [
                    $tag2,
                    $tag3,
                ],
            ],
            [
                'slug' => '12th-post-slug',
                'tags' => [
                    $tag3,
                    $tag1,
                ],
            ],
            [
                'slug' => '13th-post-slug',
                'tags' => [
                    $tag1,
                    $tag2,
                    $tag3,
                ],
            ],
            [
                'slug' => '14th-post-slug',
                'tags' => [
                    $tag1,
                    $tag3,
                ],
            ],
            [
                'slug' => '15th-post-slug',
                'tags' => [$tag3],
            ],
            [
                'slug' => '16th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '17th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '18th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '19th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '20th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '21th-post-slug',
                'tags' => [],
            ],
            [
                'slug' => '22th-post-slug',
                'tags' => [$tag3],
            ],
        ];

        $totalPosts = count($posts);
        foreach ($posts as $key => $postDetails) {
            DateTimeFactory::alwaysReturn(new \DateTimeImmutable(sprintf('-%d minute', $totalPosts--)));
            $slug = $postDetails['slug'];
            $post = new Post(
                $slug,
                'Some title ' . $key,
                'Some preview of ' . $slug,
                'Some content of ' . $slug,
                $postDetails['tags']
            );

            $post->publish();

            $em->persist($post);
        }

        $draftPost = new Post('23th-post-slug', 'Some title 23', 'Some preview of 23', 'Some content of 23');
        $draftPost->setTags($tag1, $tag2, $tag3);
        $archivedPost = new Post('24th-post-slug', 'Some title 24', 'Some preview of 24', 'Some content of 24');
        $archivedPost->setTags($tag1, $tag2, $tag3);
        $archivedPost->archive();

        $em->persist($draftPost);
        $em->persist($archivedPost);

        DateTimeFactory::alwaysReturn(null);

        $em->flush();
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
                'tags'        => array_map(
                    static function (Tag $tag) {
                        return (string) $tag;
                    },
                    $post->tags()
                ),
            ];
        }

        self::assertEquals($posts, $responseData['data']);
    }
}
