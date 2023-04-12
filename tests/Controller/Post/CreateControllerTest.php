<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Entity\Post;
use App\FunctionalTestCase;

class CreateControllerTest extends FunctionalTestCase
{
    private const API_URI = '/api/posts';

    public function testBadRequest(): void
    {
        $response = $this->sendRequest('POST', self::API_URI);

        self::assertEquals(
            '{"code":400,"message":"Invalid request is passed"}',
            $response->getContent()
        );
    }

    public function testForbiddenAccess(): void
    {
        $data = [
            'title'   => 'Some title',
            'preview' => 'Some preview',
            'content' => 'Some content',
            'tags'    => [],
        ];

        $response = $this->sendRequest('POST', self::API_URI, $data);

        self::assertEquals(
            '{"code":403,"message":"You\u0027re not allowed to create posts"}',
            $response->getContent()
        );
    }

    public function testInvalidPostData(): void
    {
        $data = [
            'title'   => '',
            'preview' => '',
            'content' => '',
            'tags'    => [''],
        ];

        $this->authenticate('SomeHardCodedToken');

        $this->sendRequest('POST', self::API_URI, $data);

        $this->assertResponseContainsError('Invalid data', [
            'title'   => 'This value should not be blank.',
            'preview' => 'This value should not be blank.',
            'content' => 'This value should not be blank.',
            'tags[0]' => 'This value should be of type alnum.',
        ]);
    }

    public function testCreatePost(): void
    {
        $data = [
            'title'   => 'Some title',
            'preview' => 'Some preview',
            'content' => 'Some content',
            'tags'    => [
                'Tag2',
                'Tag1',
            ],
        ];

        $this->authenticate('SomeHardCodedToken');

        $response = $this->sendRequest('POST', self::API_URI, $data);
        $content  = (string) $response->getContent();

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($content);
        $responseData = (array) json_decode($content, true);

        self::assertArrayHasKey('slug', $responseData);

        /** @var Post $post */
        $post = $this->getEntityManager()
                     ->getRepository(Post::class)
                     ->findOneBy(['slug' => $responseData['slug']]);

        self::assertNotNull($post);

        self::assertJsonEqualsToData(
            $content,
            [
                'slug'        => $post->slug(),
                'title'       => 'Some title',
                'preview'     => 'Some preview',
                'content'     => 'Some content',
                'tags'        => [
                    'Tag2',
                    'Tag1',
                ],
                'createdAt'   => $post->createdAt()->format(DATE_ATOM),
                'updatedAt'   => null,
                'publishedAt' => null,
            ]
        );
    }
}
