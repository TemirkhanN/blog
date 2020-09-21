<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\CreatePost;
use App\Entity\Author;
use App\Service\Post\CreatePostService;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateController
{
    /**
     * @var CreatePostService
     */
    private $postCreator;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $security;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * Construct
     *
     * @param CreatePostService             $postCreator
     * @param AuthorizationCheckerInterface $securityChecker
     * @param ValidatorInterface            $validator
     * @param ResponseFactoryInterface      $responseFactory
     */
    public function __construct(
        CreatePostService $postCreator,
        AuthorizationCheckerInterface $securityChecker,
        ValidatorInterface $validator,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->postCreator     = $postCreator;
        $this->security        = $securityChecker;
        $this->validator       = $validator;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param Author     $author
     * @param CreatePost $postData
     *
     * @return Response
     */
    public function __invoke(Author $author, CreatePost $postData): Response
    {
        if (!$this->security->isGranted('create_post', $author)) {
            return $this->responseFactory->forbidden("You're not allowed to create posts");
        }

        $violations = $this->validator->validate($postData);
        if (count($violations)) {
            return $this->responseFactory->view($violations, 'constraints.violation', Response::HTTP_BAD_REQUEST);
        }

        try {
            $post = $this->postCreator->execute($author, $postData);
        } catch (\DomainException $e) {
            return $this->responseFactory->badRequest($e->getMessage());
        }

        return $this->responseFactory->view($post, 'post.view', Response::HTTP_CREATED);
    }
}
