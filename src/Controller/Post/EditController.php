<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Repository\PostRepositoryInterface;
use App\Dto\PostData;
use App\Lib\Response\ResponseFactoryInterface;
use App\View\PostView;
use App\View\ValidationErrorsView;
use Ser\DtoRequestBundle\Attributes\Dto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditController
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly ValidatorInterface $validator,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, #[Dto] PostData $newData): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to edit posts");
        }

        $violations = $this->validator->validate($newData);
        if ($violations->count() !== 0) {
            return $this->responseFactory->createResponse(ValidationErrorsView::create($violations));
        }

        $post = $this->postRepository->findOneBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $post->changeTitle($newData->title, $this->postRepository);
        $post->changePreview($newData->preview);
        $post->changeContent($newData->content);
        $post->setTags($newData->tags);

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse(PostView::create($post));
    }
}
