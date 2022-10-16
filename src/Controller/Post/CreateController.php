<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Service\Post\CreatePostService;
use App\Service\Post\Dto\PostData;
use App\Service\Response\ResponseFactoryInterface;
use App\View\ErrorView;
use App\View\PostView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CreateController
{
    public function __construct(
        private readonly CreatePostService $postCreator,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(PostData $postData): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to create posts");
        }

        $result = $this->postCreator->execute($postData);
        if (!$result->isSuccessful()) {
            return $this->responseFactory->createResponse(ErrorView::create($result->getError()), 400);
        }

        return $this->responseFactory->createResponse(PostView::create($result->getData()));
    }
}
