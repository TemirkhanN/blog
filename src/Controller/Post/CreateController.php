<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Entity\Post;
use App\Domain\Repository\PostRepositoryInterface;
use App\Domain\ValueObject\Slug;
use App\Dto\PostData;
use App\Lib\Response\ResponseFactoryInterface;
use App\View\PostView;
use App\View\ValidationErrorsView;
use Ser\DtoRequestBundle\Attributes\Dto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateController
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(#[Dto] PostData $postData): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to create posts");
        }

        $violations = $this->validator->validate($postData);
        if ($violations->count() !== 0) {
            return $this->responseFactory->createResponse(ValidationErrorsView::create(($violations)));
        }

        if ($this->alreadyExists($postData)) {
            return $this->responseFactory->createResponse(
                ValidationErrorsView::createPlain(['title' => 'There already exists a post with a similar title'])
            );
        }

        $post = new Post(
            $postData->title,
            $postData->preview,
            $postData->content,
            $postData->tags
        );

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse(PostView::create($post));
    }

    private function alreadyExists(PostData $postData): bool
    {
        return $this->postRepository->findOneBySlug((string) new Slug($postData->title)) !== null;
    }
}
