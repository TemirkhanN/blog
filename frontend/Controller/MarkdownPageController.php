<?php

declare(strict_types=1);

namespace Frontend\Controller;

use App\Lib\Response\Cache\TTL;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\Response;

readonly class MarkdownPageController extends AbstractBlogController
{
    /**
     * @const array<string, array{title: string, source: string}>
     */
    private const PAGES = [
        'about' => [
            'title'  => 'About me',
            'source' => 'https://raw.githubusercontent.com/TemirkhanN/cv/master/README.md',
        ],
    ];

    public function __invoke(string $name): Response
    {
        if (!isset(self::PAGES[$name])) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        $page = self::PAGES[$name];

        $response = new Response(
            $this->renderer->render(Page::MARKDOWN_PAGE, [
                'content' => file_get_contents($page['source']),
                'title'   => $page['title'],
            ])
        );

        return $this->cacheGateway->cache($response, TTL::hours(24));
    }
}
