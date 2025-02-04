<?php

declare(strict_types=1);

namespace App\View;

use App\Domain\Entity\Post;

class PostView
{
    /**
     * @param Post $post
     * @param bool $extended
     *
     * @return array{
     *  id: int,
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
            'id'          => $post->id(),
            'slug'        => $post->slug(),
            'title'       => $post->title(),
            'createdAt'   => DateTimeView::create($post->createdAt()),
            'updatedAt'   => $post->updatedAt() ? DateTimeView::create($post->updatedAt()) : null,
            'publishedAt' => $post->publishedAt() ? DateTimeView::create($post->publishedAt()) : null,
            'preview'     => $post->preview(),
            'tags'        => $post->tags(),
        ];

        if ($extended) {
            $view['content'] = $post->content();
        }

        return $view;
    }
}
