<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\PostData;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\Response\ResponseFactoryInterface;
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

        // TODO slug is unknown outside of the scope. Need a way to validate it without domainexception
        $post = new Post(
            $this->postRepository,
            $postData->title,
            $postData->preview,
            $postData->content,
            $postData->tags
        );

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse(PostView::create($post));
    }
}
