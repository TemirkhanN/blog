<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Dto\PostData;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;

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

    public function execute(PostData $newData, Post $post): void
    {
        $newSlug = $this->slugGenerator->regenerate($post->slug(), $newData->title);

        if ($post->slug() !== $newSlug && $this->repository->findOneBySlug($newSlug)) {
            throw new \DomainException('There already exists the post with similar title');
        }

        $post->changeSlug($newSlug);
        $post->changeTitle($newData->title);
        $post->changePreview($newData->preview);
        $post->changeContent($newData->content);

        $newTags = $this->tagService->createTags($newData->tags);
        $post->setTags(...$newTags);

        $this->repository->save($post);
    }
}
