<?php

declare(strict_types=1);

namespace Frontend\Resource\View;

enum Page: string
{
    case LOGIN = 'pages/admin/login.html.twig';
    case POST_EDIT = 'pages/admin/post_edit.html.twig';
    case POSTS = 'pages/posts.html.twig';
    case POST = 'pages/post.html.twig';
    case ERROR_NOT_FOUND = 'pages/system/404.html.twig';
}
