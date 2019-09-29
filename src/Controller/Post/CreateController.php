<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\CreatePost;
use App\Service\Post\CreatePostService;
use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->security->isGranted('create_post')) {
            return $this->responseFactory->forbidden("You're not allowed to create posts");
        }

        $data       = $request->request->all();
        $violations = $this->validator->validate($data, CreatePost::getConstraints());
        if (count($violations)) {
            return $this->responseFactory->view($violations, 'constraints.violation', Response::HTTP_BAD_REQUEST);
        }

        $author = 'Temirkhan'; // TODO replace
        $post   = $this->postCreator->execute($author, new CreatePost($data));

        return $this->responseFactory->view($post, 'post.view', Response::HTTP_CREATED);
    }
}
