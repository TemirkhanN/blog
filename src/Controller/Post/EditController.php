<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\Dto\PostData;
use App\Service\Post\EditPost;
use App\Service\Post\PostListService;
use App\Service\Response\ResponseFactoryInterface;
use App\View\PostView;
use App\View\ViolationsView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditController
{
    public function __construct(
        private readonly EditPost $postUpdater,
        private readonly PostListService $postListService,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ValidatorInterface $validator,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug, PostData $postData): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to edit posts");
        }

        $post = $this->postListService->getPostBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        $violations = $this->validator->validate($postData);
        if (count($violations)) {
            return $this->responseFactory->createResponse(
                ViolationsView::create($violations),
                Response::HTTP_BAD_REQUEST
            );
        }

        $result = $this->postUpdater->execute($postData, $post);
        if (!$result->isSuccessful()) {
            return $this->responseFactory->badRequest($result->getError());
        }

        return $this->responseFactory->createResponse(PostView::create($post));
    }
}
