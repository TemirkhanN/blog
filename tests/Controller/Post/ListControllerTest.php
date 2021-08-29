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
     * @param array  $query
     * @param string $error
     *
     * @dataProvider badRequestProvider
     */
    public function testBadRequest(array $query, string $error): void
    {
        $response = $this->sendRequest('GET', self::API_URL . '?' . http_build_query($query));

        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals($error, $response->getContent());
    }

    public function badRequestProvider(): iterable
    {
        # 1 Invalid offset
        yield [
            'query' => ['offset' => -1],
            'error' => '{"code":400,"message":"Offset can not be less than 0"}',
        ];

        # 2 Invalid limit
        yield [
            'query' => ['limit' => 0],
            'error' => '{"code":400,"message":"Limit can not be less than 1 or too high"}',
        ];

        # 3 Too high limit
        yield [
            'query' => ['limit' => 21],
            'error' => '{"code":400,"message":"Limit can not be less than 1 or too high"}',
        ];
    }

    /**
     * @param array    $query
     * @param string[] $matchingPosts
     * @param array{
     *     limit: int,
     *     offset: int,
     *     total: int
     *     } $pagination
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

    public function postFilterProvider(): iterable
    {
        # 0 Last 10 posts(default behaviour)
        yield [
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

        # 1 Last 5 posts
        yield [
            'query'         => [
                'limit' => 5,
            ],
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

        # 2 Last 5 posts from 4th position
        yield [
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

        # 3 Maximum allowed limit
        yield [
            'query'         => [
                'limit' => 20,
            ],
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

        # 4 Offset and limit over total amount
        yield [
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

        # 5 Posts tagged with "SomeTag"
        yield [
            'query'         => [
                'tag' => 'SomeTag',
            ],
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

        # 6 Posts tagged with "AnotherTag"
        yield [
            'query'         => [
                'tag' => 'AnotherTag',
            ],
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

        # 7 Posts tagged with "OneMoreTag"
        yield [
            'query'         => [
                'tag' => 'OneMoreTag',
            ],
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

        # 7 Posts tagged with "OneMoreTag" with limit and offset
        yield [
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
                'tags' => [$tag1, $tag2],
            ],
            [
                'slug' => '11th-post-slug',
                'tags' => [$tag2, $tag3],
            ],
            [
                'slug' => '12th-post-slug',
                'tags' => [$tag3, $tag1],
            ],
            [
                'slug' => '13th-post-slug',
                'tags' => [$tag1, $tag2, $tag3],
            ],
            [
                'slug' => '14th-post-slug',
                'tags' => [$tag1, $tag3],
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
            $post = new Post($slug, 'Some title ' . $key, 'Some preview of ' . $slug, 'Some content of ' . $slug);

            foreach ($postDetails['tags'] as $tag) {
                $post->addTag($tag);
            }

            $em->persist($post);
        }

        DateTimeFactory::alwaysReturn(null);

        $em->flush();
    }

    private function assertResponseContainsPosts(array $postsSlugs, Response $response): void
    {
        $content = $response->getContent();
        self::assertJson($content);
        $responseData = json_decode($content, true);

        self::assertArrayHasKey('data', $responseData);

        self::assertEquals(count($postsSlugs), count($responseData['data']), 'Posts amount mismatch');

        $repository = $this->getEntityManager()->getRepository(Post::class);

        $posts = [];
        foreach ($postsSlugs as $slug) {
            /** @var Post $post */
            $post = $repository->findOneBy(['slug' => $slug]);

            self::assertNotNull($post);

            $posts[] = [
                'slug'        => $post->getSlug(),
                'title'       => $post->getTitle(),
                'publishedAt' => $post->getPublishedAt()->format(\DateTimeInterface::W3C),
                'preview'     => $post->getPreview(),
                'tags'        => array_map(
                    static function (Tag $tag) {
                        return (string)$tag;
                    },
                    $post->getTags()
                ),
            ];
        }

        self::assertEquals($posts, $responseData['data']);
    }
}
