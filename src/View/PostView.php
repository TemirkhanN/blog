<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Post;
use App\Entity\Tag;
use Temirkhan\View\ViewInterface;

class PostView implements ViewInterface
{
    private bool $isFull;

    public function __construct(bool $isFull = true)
    {
        $this->isFull = $isFull;
    }

    public function getView($context)
    {
        if (!$context instanceof Post) {
            return null;
        }

        $view = [
            'slug'        => $context->getSlug(),
            'title'       => $context->getTitle(),
            'publishedAt' => $context->getPublishedAt()->format(\DateTimeInterface::ATOM),
            'tags'        => array_map(static function (Tag $tag) {
                return (string) $tag;
            }, $context->getTags()),
        ];

        if ($this->isFull) {
            $view['content'] = $context->getContent();
        } else {
            $view['preview'] = $context->getPreview();
        }

        return $view;
    }
}
