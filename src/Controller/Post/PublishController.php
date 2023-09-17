<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Entity\Exception\ImpossibleTransitionException;
use App\Repository\PostRepositoryInterface;
use App\Service\Response\Dto\SystemMessage;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PublishController
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly AuthorizationCheckerInterface $security,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(string $slug): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to modify posts");
        }

        $post = $this->postRepository->findOneBySlug($slug);
        if ($post === null) {
            return $this->responseFactory->notFound("Publication doesn't exist");
        }

        try {
            $post->publish();
        } catch (ImpossibleTransitionException $e) {
            return $this->responseFactory->createResponse(new SystemMessage($e->getMessage()));
        }

        $this->postRepository->save($post);

        return $this->responseFactory->createResponse([]);
    }
}
