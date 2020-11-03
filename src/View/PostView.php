<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Post;
use App\Service\TextFormatter;
use Temirkhan\View\ViewInterface;

class PostView implements ViewInterface
{
    /**
     * @var bool
     */
    private $isFull;

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
            'author'      => $context->getAuthor()->getName(),
            'publishedAt' => $context->getPublishedAt()->format('Y-m-d H:i:s'),
        ];

        if ($this->isFull) {
            $view['content'] = $context->getContent();
        } else {
            $view['preview'] = TextFormatter::cutFirstSentences($context->getContent(), 4);
        }

        return $view;
    }
}
