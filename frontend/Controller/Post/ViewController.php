<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use App\Lib\Response\Cache\TTL;
use Frontend\API\ApiError;
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

        $fetchedPost = $this->blogApi->getPost($id);
        if (!$fetchedPost->isSuccessful()) {
            $error = $fetchedPost->getError();
            if ($error->getCode() === ApiError::RESOURCE_NOT_FOUND->value) {
                return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
            }

            return new Response($this->renderer->render(Page::ERROR, ['error' => 'Currently unavailable']), 503);
        }

        $post = $fetchedPost->getData();
        if ($post->slug !== $slug) {
            $newUri = $this->getPostUri($post);

            return new RedirectResponse($newUri, Response::HTTP_PERMANENTLY_REDIRECT);
        }

        $response = new Response($this->renderer->render(Page::POST, ['post' => $post]));

        return $this->cacheGateway->cache($response, TTL::hours(1));
    }

    private function attemptToRedirectToLatestUrl(string $slug): Response
    {
        $fetchedPost = $this->blogApi->getPostBySlug($slug);
        if (!$fetchedPost->isSuccessful()) {
            $error = $fetchedPost->getError();
            if ($error->getCode() === ApiError::RESOURCE_NOT_FOUND->value) {
                return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
            }

            return new Response($this->renderer->render(Page::ERROR, ['error' => 'Currently unavailable']), 503);
        }

        $newUri = $this->getPostUri($fetchedPost->getData());

        return new RedirectResponse($newUri, Response::HTTP_PERMANENTLY_REDIRECT);
    }
}
