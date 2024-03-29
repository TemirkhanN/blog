<?php

declare(strict_types=1);

namespace Frontend\Controller;

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

    public function __invoke(string $name): Response
    {
        if (!isset(self::$pages[$name])) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        $page = self::$pages[$name];

        $response = new Response(
            $this->renderer->render(Page::MARKDOWN_PAGE, [
                'content' => file_get_contents($page['source']),
                'title'   => $page['title'],
            ])
        );

        return $this->cacheGateway->cache($response, TTL::hours(24));
    }
}
