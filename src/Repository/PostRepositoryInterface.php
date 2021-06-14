<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostCollection;

interface PostRepositoryInterface
{
    public function getPosts(int $limit, int $offset): PostCollection;

    public function findPostsByTag(string $tag, int $limit, int $offset): PostCollection;

    public function countPosts(): int;

    public function countPostsByTag(string $tag): int;

    public function findOneBySlug(string $slug): ?Post;

    public function save(Post $post): void;
}
