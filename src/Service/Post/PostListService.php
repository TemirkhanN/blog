<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\PostFilter;
use TemirkhanN\Generic\Collection\CollectionInterface;

class PostListService
{
    public function __construct(private readonly PostRepositoryInterface $postRepository)
    {
    }

    /**
     * @param PostFilter $filter
     *
     * @return CollectionInterface<Post>
     */
    public function getPosts(PostFilter $filter): CollectionInterface
    {
        return $this->postRepository->getPosts($filter);
    }

    public function countPosts(PostFilter $filter): int
    {
        return $this->postRepository->countPosts($filter);
    }

    public function getPostBySlug(string $slug): ?Post
    {
        return $this->postRepository->findOneBySlug($slug);
    }
}
