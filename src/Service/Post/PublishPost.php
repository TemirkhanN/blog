<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Exception\ImpossibleTransitionException;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Result;

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
     * @return Result<null>
     */
    public function execute(Post $post): Result
    {
        try {
            $post->publish();
        } catch (ImpossibleTransitionException $e) {
            return Result::error($e->getMessage());
        }

        $this->postRepository->save($post);

        return Result::success(null);
    }
}
