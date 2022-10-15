<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Service\Post\Dto\PostFilter;
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

    public function findOneBySlug(string $slug): ?Post;

    public function save(Post $post): void;
}
