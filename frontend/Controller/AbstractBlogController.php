<?php

declare(strict_types=1);

namespace Frontend\Controller;

use Frontend\API\Client;
use Frontend\Service\Renderer;

abstract class AbstractBlogController
{
    public function __construct(
        protected readonly Client $blogApi,
        protected readonly Renderer $renderer
    ) {}
}
