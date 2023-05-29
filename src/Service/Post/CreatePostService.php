<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\InvalidData;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class CreatePostService
{
    public function __construct(
        private readonly PostRepositoryInterface $repository,
        private readonly TagService $tagService
    ) {
    }

    /**
     * @param string $title
     * @param string $preview
     * @param string $content
     * @param string[] $tags
     *
     * @return ResultInterface<Post>
     */
    public function execute(string $title, string $preview, string $content, array $tags = []): ResultInterface
    {
        if ($title === '' || $preview === '' || $content === '') {
            return Result::error(new InvalidData('Title, preview or content data can not be empty', 0, []));
        }

        $post = new Post($title, $preview, $content);
        if ($this->repository->findOneBySlug($post->slug())) {
            return Result::error(Error::create('There already exists a post with a similar title'));
        }

        $this->tagService->addTags($post, $tags);
        $this->repository->save($post);

        return Result::success($post);
    }
}
