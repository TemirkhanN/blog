<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Entity\Post;
use App\FunctionalTestCase;

class ViewControllerTest extends FunctionalTestCase
{
    private const ENDPOINT     = '/api/posts/%d';
    private const ENDPOINT_ALT = '/api/posts/%s';

    protected function setUp(): void
    {
        parent::setUp();

        $draftPost    = $this->createPost('Draft post title', 'Preview', 'Content');
        $archivedPost = $this->createPost('Archived post title', 'Preview', 'Content');
        $archivedPost->archive();
        $publishedPost1 = $this->createPost('Some title', 'Some preview', 'Some content');
        $publishedPost1->publish();
        $publishedPost2 = $this->createPost('Another title', 'Preview', 'Content');
        $publishedPost2->publish();

        $this->saveState($draftPost);
        $this->saveState($archivedPost);
        $this->saveState($publishedPost1);
        $this->saveState($publishedPost2);
    }

    /**
     * @param string $slug
     *
     * @dataProvider unreachablePostSlugProvider
     */
    public function testNotFound(string $slug): void
    {
        $post = $this->findPostBySlug($slug);

        $response = $this->sendRequest('GET', sprintf(self::ENDPOINT, $post?->id() ?? 123));

        self::assertEquals(
            '{"code":404,"message":"Publication doesn\u0027t exist"}',
            $response->getContent()
        );
    }

    /**
     * @return iterable<array{0: string}>
     */
    public static function unreachablePostSlugProvider(): iterable
    {
        yield 'archived post' => ['2023-12-27_Archived-post-title'];

        yield 'draft post' => ['2023-12-27_Draft-post-title'];

        yield 'non existent post' => ['some-non-existent-post-slug'];
    }

    /**
     * @param string $slug
     *
     * @dataProvider publishedPostsSlugProvider
     */
    public function testView(string $slug): void
    {
        $post = $this->findPostBySlug($slug);
        self::assertNotNull($post);

        $response = $this->sendRequest('GET', sprintf(self::ENDPOINT, $post->id()));

        self::assertEquals(200, $response->getStatusCode());

        self::assertNotNull($post);
        self::assertNotNull($post->publishedAt());
        self::assertNotNull($post->updatedAt());
        self::assertJsonEqualsToData(
            (string) $response->getContent(),
            [
                'id'          => $post->id(),
                'slug'        => $post->slug(),
                'title'       => $post->title(),
                'preview'     => $post->preview(),
                'content'     => $post->content(),
                'tags'        => $post->tags(),
                'createdAt'   => $post->createdAt()->format(DATE_ATOM),
                'updatedAt'   => $post->updatedAt()->format(DATE_W3C),
                'publishedAt' => $post->publishedAt()->format(DATE_ATOM),
            ]
        );
    }
    /**
     * @param string $slug
     *
     * @dataProvider publishedPostsSlugProvider
     */
    public function testViewBySlug(string $slug): void
    {
        $response = $this->sendRequest('GET', sprintf(self::ENDPOINT_ALT, $slug));

        self::assertEquals(200, $response->getStatusCode());

        $post = $this->findPostBySlug($slug);
        self::assertNotNull($post);
        self::assertNotNull($post->publishedAt());
        self::assertNotNull($post->updatedAt());
        self::assertJsonEqualsToData(
            (string) $response->getContent(),
            [
                'id'          => $post->id(),
                'slug'        => $post->slug(),
                'title'       => $post->title(),
                'preview'     => $post->preview(),
                'content'     => $post->content(),
                'tags'        => $post->tags(),
                'createdAt'   => $post->createdAt()->format(DATE_ATOM),
                'updatedAt'   => $post->updatedAt()->format(DATE_W3C),
                'publishedAt' => $post->publishedAt()->format(DATE_ATOM),
            ]
        );
    }

    /**
     * @return iterable<array{0: string}>
     */
    public function publishedPostsSlugProvider(): iterable
    {
        yield ['2023-12-27_Some-title'];

        yield ['2023-12-27_Another-title'];
    }

    private function findPostBySlug(string $slug): ?Post
    {
        return $this->getEntityManager()
            ->getRepository(Post::class)
            ->findOneBy(['slug' => $slug]);
    }
}
