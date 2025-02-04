<?php

declare(strict_types=1);

namespace Frontend\Controller;

use App\Lib\Response\Cache\CacheGatewayInterface;
use Frontend\API\Client;
use Frontend\API\Model\Post;
use Frontend\Service\Renderer;
use Symfony\Component\Routing\RouterInterface;

readonly abstract class AbstractBlogController
{
    public function __construct(
        protected Client $blogApi,
        protected Renderer $renderer,
        protected CacheGatewayInterface $cacheGateway,
        protected RouterInterface $router
    ) {
    }

    protected function getPostUri(Post $post): string
    {
        return $this->router->generate('blog_post', ['id' => $post->id, 'slug' => $post->slug]);
    }
}
