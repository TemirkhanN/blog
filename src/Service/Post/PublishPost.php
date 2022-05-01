<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Exception\ImpossibleTransitionException;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;

class PublishPost
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param Post $post
     *
     * @return void
     *
     * @throws ImpossibleTransitionException
     */
    public function execute(Post $post): void
    {
        $post->publish();
        $this->postRepository->save($post);
    }
}
