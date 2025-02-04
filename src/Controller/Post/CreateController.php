<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Entity\Post;
use App\Domain\Repository\PostRepositoryInterface;
use App\Dto\PostData;
use App\Lib\Response\ResponseFactoryInterface;
use App\View\PostView;
use App\View\ValidationErrorsView;
use Ser\DtoRequestBundle\Attributes\Dto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class CreateController
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private AuthorizationCheckerInterface $security,
        private ResponseFactoryInterface $responseFactory,
        private ValidatorInterface $validator
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

        $post = new Post(
            $postData->title,
            $postData->preview,
            $postData->content,
            $postData->tags
        );

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse(PostView::create($post));
    }
}
