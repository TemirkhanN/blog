<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use App\Service\Response\Cache\TTL;
use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListController extends AbstractBlogController
{
    private const POSTS_PER_PAGE = 5;

    public function __invoke(Request $request, int $page = 1, ?string $tag = null): Response
    {
        $posts = $this->blogApi->getPosts($page, self::POSTS_PER_PAGE, $tag);

        if ($posts->count() === 0) {
            if ($page !== 1) {
                return new RedirectResponse('/');
            }

            if ($tag !== null) {
                return $this->renderer->render(Page::ERROR, ['error' => 'NOT FOUND']);
            }
        }

        return $this->cacheGateway->cache($this->renderer->render(Page::POSTS, ['posts' => $posts]), TTL::hours(1));
    }
}
