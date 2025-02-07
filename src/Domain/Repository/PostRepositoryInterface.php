<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Post;
use TemirkhanN\Generic\Collection\CollectionInterface;

interface PostRepositoryInterface
{
    /**
     * @param PostFilter $filter
     *
     * @return CollectionInterface<Post>
     */
    public function getPosts(PostFilter $filter): CollectionInterface;

    public function countPosts(PostFilter $filter): int;

    public function findOneById(int $id): ?Post;
    public function findOneBySlug(string $slug): ?Post;

    public function save(Post $post): void;
}
