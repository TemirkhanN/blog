<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Post;
use App\Entity\Tag;

class PostView
{
    /**
     * @param Post $post
     * @param bool $extended
     *
     * @return array{
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
    public static function create(Post $post, bool $extended = true): array
    {
        $view = [
            'slug'        => $post->slug(),
            'title'       => $post->title(),
            'createdAt'   => DateTimeView::create($post->createdAt()),
            'updatedAt'   => $post->updatedAt() ? DateTimeView::create($post->updatedAt()) : null,
            'publishedAt' => $post->publishedAt() ? DateTimeView::create($post->publishedAt()) : null,
            'preview'     => $post->preview(),
            'tags'        => array_map(
                static function (Tag $tag) {
                    return (string) $tag;
                },
                $post->tags()
            ),
        ];

        if ($extended) {
            $view['content'] = $post->content();
        }

        return $view;
    }
}
