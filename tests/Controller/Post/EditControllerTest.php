<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Entity\Tag;
use App\FunctionalTestCase;

class EditControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/api/posts/%s';

    public function testBadRequest(): void
    {
        $postSlug = 'Some-slug-123';

        $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $postSlug));

        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(
            '{"code":400,"message":"Invalid request is passed"}',
            $response->getContent()
        );
    }

    public function testForbiddenAccess(): void
    {
        $postSlug = 'Some-slug-123';

        $newData = [
            'title'   => 'Another title',
            'preview' => 'Another preview',
            'content' => 'Another content',
            'tags'    => [
                'SomeTag2',
                'SomeTag3',
            ],
        ];

        $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $postSlug), $newData);

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(
            '{"code":403,"message":"You\u0027re not allowed to edit posts"}',
            $response->getContent()
        );
    }

    public function testNotFound(): void
    {
        $postSlug = 'Some-slug-123';
        $newData  = [
            'title'   => 'Another title',
            'preview' => 'Another preview',
            'content' => 'Another content',
            'tags'    => [
                'SomeTag2',
                'SomeTag3',
            ],
        ];

        $this->authenticate('SomeHardCodedToken');
        $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $postSlug), $newData);

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals(
            '{"code":404,"message":"Publication doesn\u0027t exist"}',
            $response->getContent()
        );
    }

    public function testInvalidPostData(): void
    {
        $postSlug = 'Some-slug-123';

        $post = $this->createPost(
            $postSlug,
            'Some title',
            'Some preview',
            'Some content',
            [
                'SomeTag1',
                'SomeTag2',
            ]
        );

        $newData = [
            'title'   => '',
            'preview' => '',
            'content' => '',
            'tags'    => [''],
        ];

        $this->authenticate('SomeHardCodedToken');

        $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $postSlug), $newData);

        self::assertEquals(400, $response->getStatusCode());
        self::assertJsonEqualsToData(
            (string) $response->getContent(),
            [
                'title'   => 'This value should not be blank.',
                'preview' => 'This value should not be blank.',
                'content' => 'This value should not be blank.',
                'tags[0]' => 'This value should be of type alnum.',
            ]
        );
    }

    public function testSuccess(): void
    {
        $postSlug = 'Some-slug-123';
        $post     = $this->createPost(
            $postSlug,
            'Some title',
            'Some preview',
            'Some content',
            [
                'SomeTag1',
                'SomeTag2',
            ]
        );

        $newData = [
            'title'   => 'Another title',
            'preview' => 'Another preview',
            'content' => 'Another content',
            'tags'    => [
                'SomeTag2',
                'SomeTag3',
            ],
        ];

        $this->authenticate('SomeHardCodedToken');
        $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $postSlug), $newData);

        self::assertEquals(200, $response->getStatusCode());
        $this->assertPostModified($post, $newData);
    }

    /**
     * @param string        $slug
     * @param string        $title
     * @param string        $preview
     * @param string        $content
     * @param array<string> $tags
     *
     * @return Post
     */
    private function createPost(
        string $slug,
        string $title,
        string $preview,
        string $content,
        array $tags
    ): Post {
        $post = new Post($slug, $title, $preview, $content);

        foreach ($tags as $tagName) {
            $tag = new Tag($tagName);
            $this->saveState($tag);
            $post->addTag($tag);
        }
        $this->saveState($post);

        return $post;
    }

    /**
     * @param Post                                                                        $post
     * @param array{title: string, preview: string, content: string, tags: array<string>} $withData
     *
     * @return void
     */
    private function assertPostModified(Post $post, array $withData): void
    {
        $this->refreshState($post);

        self::assertEquals($withData['title'], $post->title());
        self::assertEquals($withData['preview'], $post->preview());
        self::assertEquals($withData['content'], $post->content());

        $postTags = array_map(function (Tag $tag): string {
            return $tag->name();
        }, $post->tags());
        self::assertEquals($withData['tags'], $postTags);
    }
}
