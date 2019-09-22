<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Dto\CreatePost;
use App\Service\Post\CreatePostService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Temirkhan\View\ViewFactoryInterface;

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
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * Construct
     *
     * @param CreatePostService             $postCreator
     * @param AuthorizationCheckerInterface $securityChecker
     * @param ValidatorInterface            $validator
     * @param ViewFactoryInterface          $viewFactory
     */
    public function __construct(
        CreatePostService $postCreator,
        AuthorizationCheckerInterface $securityChecker,
        ValidatorInterface $validator,
        ViewFactoryInterface $viewFactory
    ) {
        $this->postCreator = $postCreator;
        $this->security    = $securityChecker;
        $this->validator   = $validator;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (!$this->security->isGranted('create_post')) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        $data = $request->request->all();
        $violations = $this->validator->validate($data, CreatePost::getConstraints());
        if (count($violations)) {
            $view = $this->viewFactory->createView('constraints.violation', $violations);

            return new JsonResponse($view, Response::HTTP_BAD_REQUEST);
        }

        $author = 'Temirkhan'; // TODO replace
        $post   = $this->postCreator->execute($author, new CreatePost($data));

        $view = $this->viewFactory->createView('post.view', $post);

        return new JsonResponse($view, Response::HTTP_CREATED);
    }
}
