<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Dto\CreatePost;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;

class CreatePostService
{
    /** @var PostRepositoryInterface<Post> */
    private PostRepositoryInterface $repository;

    /** @param PostRepositoryInterface<Post> $postRepository */
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->repository = $postRepository;
    }

    /**
     * @param CreatePost $data
     *
     * @return Post
     *
     * @throws \DomainException
     */
    public function execute(CreatePost $data): Post
    {
        $post = new Post($data->title, $data->preview, $data->content);
        if ($this->repository->findOneBySlug($post->getSlug())) {
            throw new \DomainException('There already exists the post with similar title');
        }

        $this->repository->save($post);

        return $post;
    }
}
