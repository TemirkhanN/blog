<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use App\Lib\Response\Cache\TTL;
use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class ListController extends AbstractBlogController
{
    private const POSTS_PER_PAGE = 5;

    public function __invoke(Request $request, int $page = 1, ?string $tag = null): Response
    {
        $postsFetch = $this->blogApi->getPosts($page, self::POSTS_PER_PAGE, $tag);
        if (!$postsFetch->isSuccessful()) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 'Server error']), 503);
        }

        $posts = $postsFetch->getData();
        if ($posts->count() === 0) {
            if ($page !== 1) {
                return new RedirectResponse('/');
            }

            if ($tag !== null) {
                return new Response($this->renderer->render(Page::ERROR, ['error' => 'NOT FOUND']), 404);
            }
        }

        $response = new Response($this->renderer->render(Page::POSTS, ['posts' => $posts]));

        return $this->cacheGateway->cache($response, TTL::hours(1));
    }
}
