<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Entity\PostCollection;
use App\Repository\PostRepositoryInterface;

class PostListService
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPosts(int $offset, int $limit): PostCollection
    {
        return $this->postRepository->getPosts($limit, $offset);
    }

    public function getPostsByTag(string $tag, int $offset, int $limit): PostCollection
    {
        return $this->postRepository->findPostsByTag($tag, $limit, $offset);
    }

    public function countPosts(?string $withTag = null): int
    {
        if ($withTag === null) {
            return $this->postRepository->countPosts();
        }

        return $this->postRepository->countPostsByTag($withTag);
    }

    public function getPostBySlug(string $slug): ?Post
    {
        return $this->postRepository->findOneBySlug($slug);
    }
}
