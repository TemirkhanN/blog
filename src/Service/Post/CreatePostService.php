<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\InvalidData;
use App\Service\Post\Dto\PostData;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class CreatePostService
{
    public function __construct(
        private readonly PostRepositoryInterface $repository,
        private readonly TagService $tagService,
        private readonly SlugGenerator $slugGenerator,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @param PostData $data
     *
     * @return ResultInterface<Post>
     */
    public function execute(PostData $data): ResultInterface
    {
        $violations = $this->validator->validate($data);
        if ($violations->count() !== 0) {
            return Result::error(InvalidData::fromConstraintsViolation($violations));
        }

        $slug = $this->slugGenerator->generate($data->title);
        if ($this->repository->findOneBySlug($slug)) {
            return Result::error(Error::create('There already exists a post with a similar title'));
        }

        $tags = $this->tagService->createTags($data->tags);
        $post = new Post($slug, $data->title, $data->preview, $data->content, $tags);

        $this->repository->save($post);

        return Result::success($post);
    }
}
