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

class EditPost
{
    public function __construct(
        private readonly PostRepositoryInterface $repository,
        private readonly TagService $tagService,
        private readonly SlugGenerator $slugGenerator,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @param PostData $newData
     * @param Post     $post
     *
     * @return ResultInterface<null>
     */
    public function execute(PostData $newData, Post $post): ResultInterface
    {
        $violations = $this->validator->validate($newData);
        if ($violations->count() !== 0) {
            return Result::error(InvalidData::fromConstraintsViolation($violations));
        }

        $newSlug = $this->slugGenerator->regenerate($post->slug(), $newData->title);

        if ($post->slug() !== $newSlug && $this->repository->findOneBySlug($newSlug)) {
            return Result::error(Error::create('There already exists the post with similar title'));
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
