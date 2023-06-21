<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\PostData;
use App\Repository\PostRepositoryInterface;
use App\Service\Post\PostListService;
use App\Service\Response\ResponseFactoryInterface;
use App\View\PostView;
use App\View\ValidationErrorsView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditController
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly PostListService $postListService,
        private readonly ValidatorInterface $validator,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, PostData $newData): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to edit posts");
        }

        $violations = $this->validator->validate($newData);
        if ($violations->count() !== 0) {
            return $this->responseFactory->createResponse(ValidationErrorsView::create($violations));
        }

        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $post->changeTitle($newData->title);
        $post->changePreview($newData->preview);
        $post->changeContent($newData->content);
        $post->setTags($newData->tags);

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse(PostView::create($post));
    }
}
