<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Entity\PostCollection;
use App\Repository\PostRepositoryInterface;

class PostListService
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * Constructor
     *
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return PostCollection
     */
    public function getPublishedPosts(int $offset, int $limit): PostCollection
    {
        return $this->postRepository->getPosts($limit, $offset);
    }

    /**
     * @param string $slug
     *
     * @return Post|null
     */
    public function getPostBySlug(string $slug): ?Post
    {
        return $this->postRepository->findOneBySlug($slug);
    }
}
