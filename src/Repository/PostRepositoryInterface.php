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
    public static function getPosts(PostFilter $filter): CollectionInterface;

    public static function countPosts(PostFilter $filter): int;

    public static function findOneBySlug(string $slug): ?Post;

    public static function save(Post $post): void;
}
