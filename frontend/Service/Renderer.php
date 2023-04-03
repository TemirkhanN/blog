<?php

declare(strict_types=1);

namespace Frontend\Service;

use Frontend\Resource\View\Template;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Renderer
{
    public function __construct(private readonly Environment $twig) {}

    public function render(Template $template, array $context = []): Response
    {
        return new Response($this->twig->render($template->value, $context));
    }
}
