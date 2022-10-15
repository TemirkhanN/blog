<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\Dto\PostData;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class CreatePostService
{
    public function __construct(
        private readonly PostRepositoryInterface $repository,
        private readonly TagService $tagService,
        private readonly SlugGenerator $slugGenerator
    ) {
    }

    /**
     * @param PostData $data
     *
     * @return ResultInterface<Post>
     */
    public function execute(PostData $data): ResultInterface
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
