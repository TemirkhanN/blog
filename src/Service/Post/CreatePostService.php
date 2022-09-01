<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\PostData;
use App\Service\Result;

class CreatePostService
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
     * @param PostData $data
     *
     * @return Result<Post>
     */
    public function execute(PostData $data): Result
    {
        $slug = $this->slugGenerator->generate($data->title);
        if ($this->repository->findOneBySlug($slug)) {
            return Result::error('There already exists the post with similar title');
        }

        $tags = $this->tagService->createTags($data->tags);
        $post = new Post($slug, $data->title, $data->preview, $data->content, $tags);

        $this->repository->save($post);

        return Result::success($post);
    }
}
