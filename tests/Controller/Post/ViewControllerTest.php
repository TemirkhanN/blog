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

        $draftPost    = $this->createPost('789draft-post-slug', 'Draft post title', 'Preview', 'Content');
        $archivedPost = $this->createPost('012archived-post-slug', 'Archived post title', 'Preview', 'Content');
        $archivedPost->archive();
        $publishedPost1 = $this->createPost('some-slug_link123', 'Some title', 'Some preview', 'Some content');
        $publishedPost1->publish();
        $publishedPost2 = $this->createPost('456another-slug_link', 'Another title', 'Preview', 'Content');
        $publishedPost2->publish();

        $this->saveState($draftPost, $archivedPost, $publishedPost1, $publishedPost2);
    }

    /**
     * @param string $slug
     *
     * @dataProvider unreachablePostSlugProvider
     */
    public function testNotFound(string $slug): void
    {
        $response = $this->sendRequest('GET', sprintf(self::API_URL, $slug));

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals(
            '{"code":404,"message":"Publication doesn\u0027t exist"}',
            $response->getContent()
        );
    }

    /**
     * @return iterable<array{0: string}>
     */
    public function unreachablePostSlugProvider(): iterable
    {
        yield 'archived post' => ['012archived-post-slug'];

        yield 'draft post' => ['789draft-post-slug'];

        yield 'non existent post' => ['some-non-existent-post-slug'];
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
        self::assertNotNull($post->publishedAt());
        self::assertNotNull($post->updatedAt());
        self::assertJsonEqualsToData(
            (string) $response->getContent(),
            [
                'slug'        => $post->slug(),
                'title'       => $post->title(),
                'preview'     => $post->preview(),
                'content'     => $post->content(),
                'tags'        => array_map(
                    function (Tag $tag): string {
                        return $tag->name();
                    },
                    $post->tags()
                ),
                'createdAt'   => $post->createdAt()->format(DATE_ATOM),
                'updatedAt'   => $post->updatedAt()->format(DATE_W3C),
                'publishedAt' => $post->publishedAt()->format(DATE_ATOM),
            ]
        );
    }

    /**
     * @return iterable<array{0: string}>
     */
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
        $this->saveState($post);

        return $post;
    }

    private function saveState(Post ...$posts): void
    {
        $em = $this->getEntityManager();
        foreach ($posts as $post) {
            $em->persist($post);
        }
        $em->flush();
    }
}
