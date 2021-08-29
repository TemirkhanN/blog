<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Entity\Post;
use App\Entity\Tag;
use App\FunctionalTestCase;

class ViewControllerTest extends FunctionalTestCase
{
    private const API_URL = '/api/posts/%s';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createPost('some-slug_link123', 'Some title', 'Some preview', 'Some content');
        $this->createPost('456another-slug_link', 'Another title', 'Preview', 'Content');
    }


    public function testNotFound(): void
    {
        $slug = 'some-non-existent-post-slug';

        $response = $this->sendRequest('GET', sprintf(self::API_URL, $slug));

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals(
            '{"code":404,"message":"Publication doesn\u0027t exist"}',
            $response->getContent()
        );
    }

    /**
     * @param string $slug
     *
     * @dataProvider slugProvider
     */
    public function testView(string $slug): void
    {
        $response = $this->sendRequest('GET', sprintf(self::API_URL, $slug));

        self::assertEquals(200, $response->getStatusCode());

        $postRepository = $this->getEntityManager()->getRepository(Post::class);
        /** @var Post $post */
        $post = $postRepository->findOneBy(['slug' => $slug]);
        self::assertNotNull($post);
        self::assertJsonEqualsToData(
            $response->getContent(),
            [
                'slug'        => $post->getSlug(),
                'title'       => $post->getTitle(),
                'preview'     => $post->getPreview(),
                'content'     => $post->getContent(),
                'tags'        => array_map(
                    function (Tag $tag): string {
                        return $tag->getName();
                    },
                    $post->getTags()
                ),
                'publishedAt' => $post->getPublishedAt()->format(DATE_ATOM),
            ]
        );
    }

    public function slugProvider(): iterable
    {
        yield ['some-slug_link123'];

        yield ['456another-slug_link'];
    }

    private function createPost(
        string $slug,
        string $title,
        string $preview,
        string $content
    ): Post {
        $post = new Post($slug, $title, $preview, $content);

        $em = $this->getEntityManager();
        $em->persist($post);
        $em->flush();

        return $post;
    }
}
