<?php

declare(strict_types=1);

namespace Frontend\Controller\Admin;

use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Frontend\Service\Access;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostListController extends AbstractBlogController
{
    private const POSTS_PER_PAGE = 10;

    /**
     * @var string[]
     */
    private array $errors = [];


    public function __invoke(Request $request, int $page, Access $access): Response
    {
        if (!$access->isAdmin()) {
            return $this->renderer->render(Page::ERROR, ['error' => 404]);
        }

        $this->performAction($request);

        $posts = $this->blogApi->getPosts($page, self::POSTS_PER_PAGE);

        return $this->renderer->render(Page::ADMIN_POST_LIST, ['posts' => $posts, 'errors' => $this->errors]);
    }

    private function performAction(Request $request): void
    {
        $publish = (string) $request->query->get('publish', '');

        if ($publish !== '') {
            $result = $this->blogApi->publishPost($publish);

            if (!$result->isSuccessful()) {
                $this->errors[] = $result->getError()->getMessage();
            }
        }
    }
}
