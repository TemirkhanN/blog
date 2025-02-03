<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Entity\Post;
use App\FunctionalTestCase;

class EditControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/api/posts/%s';

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

        self::assertEquals(
            '{"code":404,"message":"Publication doesn\u0027t exist"}',
            $response->getContent()
        );
    }

    public function testBadRequest(): void
    {
        $postSlug = 'Some-slug-123';

        $this->authenticate('SomeHardCodedToken');
        $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $postSlug));

        $this->assertResponseContainsError('Invalid data', [
            'title'   => 'This value should not be blank.',
            'preview' => 'This value should not be blank.',
            'content' => 'This value should not be blank.',
        ]);
    }

    public function testInvalidPostData(): void
    {
        $post = $this->createPost(
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

        $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $post->slug()), $newData);

        $this->assertResponseContainsError('Invalid data', [
            'title'   => 'This value should not be blank.',
            'preview' => 'This value should not be blank.',
            'content' => 'This value should not be blank.',
            'tags[0]' => 'This value should be of type alnum.',
        ]);
    }

    public function testSuccess(): void
    {
        $post = $this->createPost(
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
        $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $post->slug()), $newData);

        self::assertEquals(200, $response->getStatusCode());
        $this->assertPostModified($post, $newData);
    }

    public function testSuccessSlugUnchanged(): void
    {
        $post = $this->createPost(
            'Some title',
            'Some preview',
            'Some content',
            [
                'SomeTag1',
                'SomeTag2',
            ]
        );

        $newData = [
            'title'   => 'Some title',
            'preview' => 'Another preview',
            'content' => 'Another content',
            'tags'    => [
                'SomeTag2',
                'SomeTag3',
            ],
        ];

        $this->authenticate('SomeHardCodedToken');
        $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT, $post->slug()), $newData);

        self::assertEquals(200, $response->getStatusCode());
        $this->assertPostModified($post, $newData);
    }

    /**
     * @param Post                                                                        $post
     * @param array{title: string, preview: string, content: string, tags: array<string>} $withData
     *
     * @return void
     */
    private function assertPostModified(Post $post, array $withData): void
    {
        // This ensures that changes are actually saved to the storage(i.e., not in modified-yet-not-saved state)
        $this->refreshState($post);

        self::assertEquals($withData['title'], $post->title());
        self::assertEquals($withData['preview'], $post->preview());
        self::assertEquals($withData['content'], $post->content());
        self::assertEquals($withData['tags'], $post->tags());
    }
}
