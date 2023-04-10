<?php

declare(strict_types=1);

namespace Frontend\Service;

use Frontend\Resource\View\Page;
use Frontend\Service\RendererExtension\Markdown;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function render(Page $page, array $context = []): Response
    {
        return new Response($this->twig->render($page->value, $context));
    }
}
