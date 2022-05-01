<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Entity\Collection;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\PostFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PostListServiceTest extends TestCase
{
    /** @var PostRepositoryInterface&MockObject */
    private PostRepositoryInterface $postRepository;

    private PostListService $postListService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository  = $this->createMock(PostRepositoryInterface::class);
        $this->postListService = new PostListService($this->postRepository);
    }

    public function testCountPosts(): void
    {
        $filter = new PostFilter();

        $this->postRepository
            ->expects(self::once())
            ->method('countPosts')
            ->with(self::identicalTo($filter))
            ->willReturn(123);

        self::assertEquals(123, $this->postListService->countPosts($filter));
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

    public function testGetPosts(): void
    {
        $filter = new PostFilter();

        $this->postRepository
            ->expects(self::once())
            ->method('getPosts')
            ->with(self::identicalTo($filter))
            ->willReturn($posts = $this->createMock(Collection::class));

        $result = $this->postListService->getPosts($filter);

        self::assertSame($posts, $result);
    }
}
