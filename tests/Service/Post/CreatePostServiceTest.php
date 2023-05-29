<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreatePostServiceTest extends TestCase
{
    /** @var PostRepositoryInterface&MockObject */
    private PostRepositoryInterface $postRepository;

    /** @var ValidatorInterface&MockObject */
    private ValidatorInterface $validator;

    private CreatePostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = $this->createMock(PostRepositoryInterface::class);
        $this->validator      = $this->createMock(ValidatorInterface::class);
        $this->service        = new CreatePostService(
            $this->postRepository,
            $this->createMock(TagService::class),
            new SlugGenerator(),
            $this->validator
        );
    }

    public function testDuplicatePostCreation(): void
    {
        $title = 'Some title';
        $preview = 'Some preview';
        $content = 'Some content';
        $tags    = [];

        $expectedSlug = date('Y-m-d') . '_Some-title';
        $this->postRepository
            ->expects(self::once())
            ->method('findOneBySlug')
            ->with(self::equalTo($expectedSlug))
            ->willReturn($this->createMock(Post::class));

        $result = $this->service->execute($title, $preview, $content, $tags);
        self::assertFalse($result->isSuccessful());
        self::assertEquals('There already exists a post with a similar title', $result->getError()->getMessage());
    }

    public function testPostCreation(): void
    {
        $title = 'Some title';
        $preview = 'Some preview';
        $content = 'Some content';
        $tags    = [];

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
                self::assertEqualsWithDelta(time(), $actualPost->createdAt()->getTimestamp(), 2);
                self::assertNull($actualPost->publishedAt());
                self::assertNull($actualPost->updatedAt());
                self::assertEquals('Some title', $actualPost->title());
                self::assertEmpty($actualPost->tags());
                $expectedPost = $actualPost;

                return true;
            }));

        $result = $this->service->execute($title, $preview, $content, $tags);

        self::assertTrue($result->isSuccessful());

        $savedPost = $result->getData();
        self::assertSame($expectedPost, $savedPost);
    }
}
