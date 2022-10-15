<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\PostData;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class EditPost
{
    public function __construct(
        private readonly PostRepositoryInterface $repository,
        private readonly TagService $tagService,
        private readonly SlugGenerator $slugGenerator
    ) {
    }

    /**
     * @param PostData $newData
     * @param Post     $post
     *
     * @return ResultInterface<void>
     */
    public function execute(PostData $newData, Post $post): ResultInterface
    {
        $newSlug = $this->slugGenerator->regenerate($post->slug(), $newData->title);

        if ($post->slug() !== $newSlug && $this->repository->findOneBySlug($newSlug)) {
            return Result::error('There already exists the post with similar title');
        }

        $post->changeSlug($newSlug);
        $post->changeTitle($newData->title);
        $post->changePreview($newData->preview);
        $post->changeContent($newData->content);

        $newTags = $this->tagService->createTags($newData->tags);
        $post->setTags(...$newTags);

        $this->repository->save($post);

        return Result::success();
    }
}
