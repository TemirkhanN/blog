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
    private ?Environment $environment = null;

    public function __construct(private readonly string $templateDir)
    {
    }

    public function render(Page $template, array $context = []): Response
    {
        return new Response($this->twig()->render($template->value, $context));
    }

    private function twig(): Environment
    {
        if ($this->environment === null) {
            $this->environment = new Environment(new FilesystemLoader($this->templateDir));
            $this->environment->addExtension(new Markdown());
        }

        return $this->environment;
    }
}
