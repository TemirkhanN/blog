<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Post;
use Temirkhan\View\ViewInterface;

class PostView implements ViewInterface
{
    public function getView($context)
    {
        if (!$context instanceof Post) {
            return null;
        }

        return [
            'slug'   => $context->getSlug(),
            'title'  => $context->getTitle(),
            'author' => $context->getAuthor()->getName(),
        ];
    }
}
