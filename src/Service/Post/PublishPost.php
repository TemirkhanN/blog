<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Exception\ImpossibleTransitionException;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

/**
 * @todo this service is no longer required.
 */
class PublishPost
{
    public function __construct(private readonly PostRepositoryInterface $postRepository)
    {
    }

    /**
     * @param Post $post
     *
     * @return ResultInterface<null>
     */
    public function execute(Post $post): ResultInterface
    {
        try {
            $post->publish();
        } catch (ImpossibleTransitionException $e) {
            return Result::error(Error::create($e->getMessage()));
        }

        $this->postRepository->save($post);

        return Result::success();
    }
}
