<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Entity\PostCollection;
use App\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;

class PostListServiceTest extends TestCase
{
    private PostRepositoryInterface $postRepository;

    private PostListService $postListService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = $this->createMock(PostRepositoryInterface::class);
        $this->postListService = new PostListService($this->postRepository);
    }

    public function testCountPosts(): void
    {
        $this->postRepository
            ->expects(self::once())
            ->method('countPosts')
            ->willReturn(123);

        self::assertEquals(123, $this->postListService->countPosts());
    }

    public function testCountTaggedPosts(): void
    {
        $this->postRepository
            ->expects(self::once())
            ->method('countPostsByTag')
            ->with(self::equalTo('SomeTag'))
            ->willReturn(123);

        self::assertEquals(123, $this->postListService->countPosts('SomeTag'));
    }

    public function testGetPostBySlug(): void
    {
        $this->postRepository
            ->expects(self::once())
            ->method('findOneBySlug')
            ->with(self::equalTo('SomeSlug'))
            ->willReturn($post = $this->createMock(Post::class));

        $result = $this->postListService->getPostBySlug('SomeSlug');

        self::assertSame($post, $result);
    }

    public function testGetPostsByTag(): void
    {
        $tag = 'SomeTag';
        $offset = 1;
        $limit = 2;
        $this->postRepository
            ->expects(self::once())
            ->method('findPostsByTag')
            ->with(self::equalTo($tag), self::equalTo($limit), self::equalTo($offset))
            ->willReturn($posts = $this->createMock(PostCollection::class));

        $result = $this->postListService->getPostsByTag($tag, $offset, $limit);

        self::assertSame($posts, $result);
    }

    public function testGetPosts(): void
    {
        $offset = 1;
        $limit = 2;
        $this->postRepository
            ->expects(self::once())
            ->method('getPosts')
            ->with(self::equalTo($limit), self::equalTo($offset))
            ->willReturn($posts = $this->createMock(PostCollection::class));

        $result = $this->postListService->getPosts($offset, $limit);

        self::assertSame($posts, $result);
    }
}
