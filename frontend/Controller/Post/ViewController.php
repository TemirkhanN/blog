<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use App\Lib\Response\Cache\TTL;
use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class ViewController extends AbstractBlogController
{
    public function __invoke(string $slug, ?int $id = null): Response
    {
        if ($id === null) {
            return $this->attemptToRedirectToLatestUrl($slug);
        }

        $post = $this->blogApi->getPost($id);

        if ($post === null) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        if ($post->slug !== $slug) {
            $newUri = $this->getPostUri($post);

            return new RedirectResponse($newUri, Response::HTTP_PERMANENTLY_REDIRECT);
        }

        $response = new Response($this->renderer->render(Page::POST, ['post' => $post]));

        return $this->cacheGateway->cache($response, TTL::hours(1));
    }

    private function attemptToRedirectToLatestUrl(string $slug): Response
    {
        $post = $this->blogApi->getPostBySlug($slug);
        if ($post === null) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        $newUri = $this->getPostUri($post);

        return new RedirectResponse($newUri, Response::HTTP_PERMANENTLY_REDIRECT);
    }
}
