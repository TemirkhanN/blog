<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\PostCollection;

interface PostRepositoryInterface
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return PostCollection
     */
    public function getPosts(int $limit, int $offset): PostCollection;
}
