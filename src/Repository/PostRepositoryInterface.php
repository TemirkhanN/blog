<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Post;
use App\Service\Post\Dto\PostFilter;

interface PostRepositoryInterface
{
    /**
     * @param PostFilter $filter
     *
     * @return Collection<Post>
     */
    public function getPosts(PostFilter $filter): Collection;

    public function countPosts(PostFilter $filter): int;

    public function findOneBySlug(string $slug): ?Post;

    public function save(Post $post): void;
}
