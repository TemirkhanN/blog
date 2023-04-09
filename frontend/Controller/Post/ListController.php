<?php
declare(strict_types=1);

namespace Frontend\Controller\Post;

use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListController extends AbstractBlogController
{
    private const POSTS_PER_PAGE = 5;

    public function __invoke(Request $request, int $page, ?string $tag = null): Response
    {
        $posts = $this->blogApi->getPosts($page, self::POSTS_PER_PAGE, $tag);

        return $this->renderer->render(Page::POSTS, [
            'posts' => $posts,
        ]);
    }
}
