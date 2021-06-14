<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Post;

/**
 * @template T of Post
 */
interface PostRepositoryInterface
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return Collection<T>
     */
    public function getPosts(int $limit, int $offset): Collection;

    /**
     * @param string $tag
     * @param int    $limit
     * @param int    $offset
     *
     * @return Collection<T>
     */
    public function findPostsByTag(string $tag, int $limit, int $offset): Collection;

    public function countPosts(): int;

    public function countPostsByTag(string $tag): int;

    public function findOneBySlug(string $slug): ?Post;

    public function save(Post $post): void;
}
