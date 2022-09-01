<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\PostData;
use App\Service\Result;

class EditPost
{
    private PostRepositoryInterface $repository;
    private TagService $tagService;
    private SlugGenerator $slugGenerator;

    public function __construct(
        PostRepositoryInterface $repository,
        TagService $tagService,
        SlugGenerator $slugGenerator
    ) {
        $this->repository    = $repository;
        $this->tagService    = $tagService;
        $this->slugGenerator = $slugGenerator;
    }

    /**
     * @param PostData $newData
     * @param Post     $post
     *
     * @return Result<null>
     */
    public function execute(PostData $newData, Post $post): Result
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

        return Result::success(null);
    }
}
