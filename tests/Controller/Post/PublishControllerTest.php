<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Entity\Post;
use App\FunctionalTestCase;

class PublishControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/api/posts/%s/releases';

    public function testForbiddenAccess(): void
    {
        $postSlug = 'Some-slug-123';

        $response = $this->sendRequest('POST', sprintf(self::ENDPOINT, $postSlug));

        self::assertEquals(
            '{"code":403,"message":"You\u0027re not allowed to modify posts"}',
            $response->getContent()
        );
    }

    public function testNotFound(): void
    {
        $postSlug = 'Some-slug-123';

        $this->authenticate('SomeHardCodedToken');
        $response = $this->sendRequest('POST', sprintf(self::ENDPOINT, $postSlug));

        self::assertEquals(
            '{"code":404,"message":"Publication doesn\u0027t exist"}',
            $response->getContent()
        );
    }

    public function testPublishDraftPost(): void
    {
        $post = $this->createPost('Some title', 'Some preview', 'Some content');

        $this->authenticate('SomeHardCodedToken');
        $response = $this->sendRequest('POST', sprintf(self::ENDPOINT, $post->slug()));

        self::assertEquals('[]', $response->getContent());
        $this->assertPostIsPublished($post);
    }

    public function testPublishAlreadyPublishedPost(): void
    {
        $post = $this->createPost('Some title', 'Some preview', 'Some content');
        $post->publish();
        $this->saveState($post);

        $this->authenticate('SomeHardCodedToken');
        $response = $this->sendRequest('POST', sprintf(self::ENDPOINT, $post->slug()));

        self::assertEquals('[]', $response->getContent());
        $this->assertPostIsPublished($post);
    }

    public function testPublishArchivedPost(): void
    {
        $post = $this->createPost('Some title', 'Some preview', 'Some content');
        $post->archive();
        $this->saveState($post);

        $this->authenticate('SomeHardCodedToken');
        $response = $this->sendRequest('POST', sprintf(self::ENDPOINT, $post->slug()));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(
            '{"code":0,"message":"Transition from published to archived is impossible"}',
            $response->getContent()
        );

        $this->refreshState($post);
        self::assertTrue($post->isArchived());
    }

    private function assertPostIsPublished(Post $post): void
    {
        $this->refreshState($post);

        self::assertTrue($post->isPublished());
        self::assertNotNull($post->publishedAt());
        self::assertTrue($this->currentTime->equalTo($post->publishedAt()));
    }
}
