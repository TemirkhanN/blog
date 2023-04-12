<?php

declare(strict_types=1);

namespace Frontend\Controller;

use App\Service\Response\Cache\CacheGatewayInterface;
use App\Service\Response\Cache\TTL;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\Response;

class MarkdownPageController extends AbstractBlogController
{
    /**
     * @var array<string, array{title: string, source: string}>
     */
    private static array $pages = [
        'about' => [
            'title'  => 'About me',
            'source' => 'https://raw.githubusercontent.com/TemirkhanN/cv/master/README.md',
        ],
    ];

    public function __invoke(string $name, CacheGatewayInterface $cacheGateway): Response
    {
        if (!isset(self::$pages[$name])) {
            return $this->renderer->render(Page::ERROR_NOT_FOUND);
        }

        $page = self::$pages[$name];

        $response = $this->renderer->render(Page::MARKDOWN_PAGE, [
            'content' => file_get_contents($page['source']),
            'title'   => $page['title'],
        ]);

        return $cacheGateway->cache($response, TTL::hours(24));
    }
}
