<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Post;
use App\Entity\Tag;
use DateTimeInterface;
use Temirkhan\View\ViewInterface;

class PostView implements ViewInterface
{
    private bool $isFull;

    public function __construct(bool $isFull = true)
    {
        $this->isFull = $isFull;
    }

    /**
     * @param mixed $context
     *
     * @return null|array{
     *  slug: string,
     *  title: string,
     *  preview: string,
     *  content?: string,
     *  createdAt: string,
     *  updatedAt: ?string,
     *  publishedAt: ?string,
     *  tags: string[]
     * }
     */
    public function getView($context)
    {
        if (!$context instanceof Post) {
            return null;
        }

        $view = [
            'slug'        => $context->slug(),
            'title'       => $context->title(),
            'createdAt'   => (string) $this->createDateTimeView($context->createdAt()),
            'updatedAt'   => $this->createDateTimeView($context->updatedAt()),
            'publishedAt' => $this->createDateTimeView($context->publishedAt()),
            'preview'     => $context->preview(),
            'tags'        => array_map(
                static function (Tag $tag) {
                    return (string) $tag;
                },
                $context->tags()
            ),
        ];

        if ($this->isFull) {
            $view['content'] = $context->content();
        }

        return $view;
    }

    private function createDateTimeView(?DateTimeInterface $dateTime): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        return $dateTime->format(DateTimeInterface::ATOM);
    }
}
