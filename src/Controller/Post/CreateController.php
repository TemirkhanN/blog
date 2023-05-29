<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\PostData;
use App\Service\InvalidData;
use App\Service\Post\CreatePostService;
use App\Service\Response\ResponseFactoryInterface;
use App\View\ErrorView;
use App\View\PostView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateController
{
    public function __construct(
        private readonly CreatePostService $postCreator,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(PostData $postData): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to create posts");
        }

        $violations = $this->validator->validate($postData);
        if ($violations->count() !== 0) {
            return $this->responseFactory->createResponse(
                ErrorView::create(InvalidData::fromConstraintsViolation($violations))
            );
        }

        $result = $this->postCreator
            ->execute($postData->title, $postData->preview, $postData->content, $postData->tags);

        if (!$result->isSuccessful()) {
            return $this->responseFactory->createResponse(ErrorView::create($result->getError()));
        }

        return $this->responseFactory->createResponse(PostView::create($result->getData()));
    }
}
