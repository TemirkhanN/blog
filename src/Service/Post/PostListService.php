<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;

class PostListService
{
    public function getPublishedPosts(int $limit, int $offset): iterable
    {
        return [
            new Post(2, 'Another title', 'Some author'),
            new Post(1, 'Some title', 'Some author'),
        ];
    }
}
