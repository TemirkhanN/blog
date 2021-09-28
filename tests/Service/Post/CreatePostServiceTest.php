<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Dto\CreatePost;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreatePostServiceTest extends TestCase
{
    /** @var PostRepositoryInterface<Post>&MockObject */
    private PostRepositoryInterface $postRepository;

    private CreatePostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = $this->createMock(PostRepositoryInterface::class);
        $this->service        = new CreatePostService($this->postRepository);
    }

    public function testDuplicatePostCreation(): void
    {
        $postData = new CreatePost([
            'title'   => 'Some title',
            'preview' => 'Some preview',
            'content' => 'Some content',
        ]);

        $expectedSlug = date('Y-m-d') . '_Some-title';
        $this->postRepository
            ->expects(self::once())
            ->method('findOneBySlug')
            ->with(self::equalTo($expectedSlug))
            ->willReturn($this->createMock(Post::class));

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('There already exists the post with similar title');

        $this->service->execute($postData);
    }

    public function testPostCreation(): void
    {
        $postData = new CreatePost([
            'title'   => 'Some title',
            'preview' => 'Some preview',
            'content' => 'Some content',
        ]);

        $expectedSlug = date('Y-m-d') . '_Some-title';
        $this->postRepository
            ->expects(self::once())
            ->method('findOneBySlug')
            ->with(self::equalTo($expectedSlug))
            ->willReturn(null);

        $expectedPost = null;
        $this->postRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function (Post $actualPost) use (&$expectedPost, $expectedSlug): bool {
                self::assertEquals($expectedSlug, $actualPost->slug());
                self::assertEquals('Some content', $actualPost->content());
                self::assertEquals('Some preview', $actualPost->preview());
                self::assertEqualsWithDelta(time(), $actualPost->publishedAt()->getTimestamp(), 2);
                self::assertEquals('Some title', $actualPost->title());
                self::assertEmpty($actualPost->tags());
                $expectedPost = $actualPost;

                return true;
            }));

        $savedPost = $this->service->execute($postData);

        self::assertSame($expectedPost, $savedPost);
    }
}
