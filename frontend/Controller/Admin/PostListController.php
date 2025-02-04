<?php

declare(strict_types=1);

namespace Frontend\Controller\Admin;

use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Frontend\Service\Access;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

readonly class PostListController extends AbstractBlogController
{
    private const POSTS_PER_PAGE = 10;


    public function __invoke(Request $request, int $page, Access $access): Response
    {
        if (!$access->isAdmin()) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        $result = $this->performAction($request);

        $posts = $this->blogApi->getPosts($page, self::POSTS_PER_PAGE);

        $errors = [];
        if (!$result->isSuccessful()) {
            $errors[] = $result->getError()->getMessage();
        }

        return new Response(
            $this->renderer->render(Page::ADMIN_POST_LIST, ['posts' => $posts, 'errors' => $errors])
        );
    }

    private function performAction(Request $request): ResultInterface
    {
        $publishingPostId = $request->query->getInt('publish');

        if ($publishingPostId !== 0) {
            $result = $this->blogApi->publishPost($publishingPostId);

            if (!$result->isSuccessful()) {
                return Result::error(Error::create($result->getError()->getMessage()));
            }
        }

        return Result::success();
    }
}
