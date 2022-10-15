<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Exception\ImpossibleTransitionException;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class PublishPost
{
    public function __construct(private readonly PostRepositoryInterface $postRepository)
    {
    }

    /**
     * @param Post $post
     *
     * @return ResultInterface<void>
     */
    public function execute(Post $post): ResultInterface
    {
        try {
            $post->publish();
        } catch (ImpossibleTransitionException $e) {
            return Result::error($e->getMessage());
        }

        $this->postRepository->save($post);

        return Result::success();
    }
}
