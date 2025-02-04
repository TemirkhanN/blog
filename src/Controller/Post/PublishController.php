<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Domain\Entity\Exception\ImpossibleTransitionException;
use App\Domain\Repository\PostRepositoryInterface;
use App\Lib\Response\Payload\SystemMessage;
use App\Lib\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class PublishController
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private AuthorizationCheckerInterface $security,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(int $id): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to modify posts");
        }

        $post = $this->postRepository->findOneById($id);
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
