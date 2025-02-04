<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Domain\Entity\Comment;
use App\Domain\Entity\Post;
use App\Domain\Repository\CommentRepositoryInterface;
use App\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReplyControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/api/posts/%d/comments/%s';
    private Post $post;
    private Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->post    = $this->createPost('Some post title', 'Some preview', 'Some content');
        $this->comment = $this->post->addComment('Some comment');
        $this->saveState($this->comment);
    }

    public function testSpamDetection(): void
    {
        $this->exceedSpamThreshold();

        $uri      = sprintf(self::ENDPOINT, $this->post->id(), $this->comment->guid());
        $payload  = ['text' => 'Some comment text'];
        $response = $this->sendRequest('POST', $uri, $payload);

        self::assertEquals(429, $response->getStatusCode());
    }

    public function testAddCommentToNonExistingPost(): void
    {
        $nonExistingPostId = 123;
        $uri               = sprintf(self::ENDPOINT, $nonExistingPostId, $this->comment->guid());
        $payload           = ['text' => 'Some comment text'];
        $response          = $this->sendRequest('POST', $uri, $payload);

        self::assertEquals('{"code":404,"message":"Target comment does not exist"}', $response->getContent());
    }

    public function testAddCommentToHiddenPost(): void
    {
        $uri      = sprintf(self::ENDPOINT, $this->post->id(), $this->comment->guid());
        $payload  = ['text' => 'Some comment text that is longer than 6 words.'];
        $response = $this->sendRequest('POST', $uri, $payload);

        self::assertEquals(
            '{"code":403,"message":"You are not allowed to comment this post"}',
            $response->getContent()
        );
    }

    public function testAddComment(): void
    {
        $this->post->publish();

        $uri      = sprintf(self::ENDPOINT, $this->post->id(), $this->comment->guid());
        $payload  = ['text' => 'Some comment text that is longer than 6 words.'];
        $response = $this->sendRequest('POST', $uri, $payload);

        self::assertEquals(200, $response->getStatusCode());
        $commentInResponse = self::getCommentDetailsFromResponse($response);
        self::assertEquals($payload['text'], $commentInResponse['comment']);

        $this->assertReplyAddedToComment($commentInResponse);
    }

    private function exceedSpamThreshold(): void
    {
        for ($i = 0; $i <= 10; $i++) {
            $comment = $this->post->addComment('Comment ' . $i);
            $this->saveState($comment);
        }
    }

    /**
     * @param array{guid: string, createdAt: string, comment: string, repliedTo: string} $expectedComment
     *
     * @return void
     */
    private function assertReplyAddedToComment(array $expectedComment): void
    {
        self::assertSame($this->comment->guid(), $expectedComment['repliedTo']);

        self::assertEquals($this->currentTime->format(DATE_ATOM), $expectedComment['createdAt']);

        $commentRepository = $this->getService(CommentRepositoryInterface::class);

        $comments = $commentRepository->findCommentsByPost($this->post);

        $commentExists = false;
        foreach ($comments as $comment) {
            if (
                $comment->guid() === $expectedComment['guid'] &&
                $comment->text() === $expectedComment['comment'] &&
                $comment->createdAt()->getTimestamp() === $this->currentTime->getTimestamp() &&
                $expectedComment['repliedTo'] === $comment->repliedTo()
            ) {
                $commentExists = true;
                break;
            }
        }

        self::assertTrue($commentExists, 'Comment does not exist');
    }

    /**
     * @param Response $response
     *
     * @return array{guid: string, createdAt: string, comment: string, repliedTo: string}
     */
    private function getCommentDetailsFromResponse(Response $response): array
    {
        $content = (string) $response->getContent();
        self::assertJson($content);

        $data = (array) json_decode($content, true);

        self::assertArrayHasKey('guid', $data);
        self::assertArrayHasKey('createdAt', $data);
        self::assertArrayHasKey('comment', $data);
        self::assertArrayHasKey('repliedTo', $data);

        // @phpstan-ignore-next-line
        return $data;
    }
}
