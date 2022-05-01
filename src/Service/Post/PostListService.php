<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Collection;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\PostFilter;

class PostListService
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param PostFilter $filter
     *
     * @return Collection<Post>
     */
    public function getPosts(PostFilter $filter): Collection
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
