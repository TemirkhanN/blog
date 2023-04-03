<?php

declare(strict_types=1);

namespace Frontend\Resource\View;

enum Template: string
{
    case POSTS = 'post/index.html.twig';
    case POST = 'post/post.html.twig';
    case ERROR_NOT_FOUND = 'system/not-found.html.twig';
}
