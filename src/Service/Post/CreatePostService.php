<?php
declare(strict_types=1);

namespace App\Service\Post;

use App\Dto\CreatePost;
use App\Entity\Author;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;

/**
 * Post creation service
 */
class CreatePostService
{
    /**
     * Post repository
     *
     * @var PostRepositoryInterface
     */
    private $repository;

    /**
     * Constructor
     *
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->repository = $postRepository;
    }

    /**
     * @param Author     $author
     * @param CreatePost $data
     *
     * @return Post
     */
    public function execute(Author $author, CreatePost $data): Post
    {
        $post = new Post($author, $data->title, $data->content);
        if ($this->repository->findOneBySlug($post->getSlug())) {
            throw new \RuntimeException(sprintf('Author already has post with very similar title'));
        }

        $this->repository->save($post);

        return $post;
    }
}
