<?php
declare(strict_types=1);

namespace App\Service\Post;

use App\Dto\CreatePost;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;

class CreatePostService
{
    /**
     * @var PostRepositoryInterface
     */
    private $repository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->repository = $postRepository;
    }

    public function execute(string $author, CreatePost $createPost): Post
    {
        static $id = 123;
        $post = new Post(++$id, $createPost->title, $createPost->content, $author);

        $this->repository->save($post);

        return $post;
    }
}
