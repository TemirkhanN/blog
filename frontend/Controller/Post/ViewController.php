<?php

declare(strict_types=1);

namespace Frontend\Controller\Post;

use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Template;
use Symfony\Component\HttpFoundation\Response;

class ViewController extends AbstractBlogController
{
    public function __invoke(string $slug): Response
    {
        $post = $this->blogApi->getPost($slug);

        if ($post === null) {
            return $this->renderer->render(Template::ERROR_NOT_FOUND);
        }

        return $this->renderer->render(Template::POST, ['post' => $post]);
    }
}
