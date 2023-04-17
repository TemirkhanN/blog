<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use App\Service\Response\Cache\TTL;
use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\Response;

class ViewController extends AbstractBlogController
{
    public function __invoke(string $slug): Response
    {
        $post = $this->blogApi->getPost($slug);

        if ($post === null) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        $response = new Response($this->renderer->render(Page::POST, ['post' => $post]));

        return $this->cacheGateway->cache($response, TTL::hours(1));
    }
}
