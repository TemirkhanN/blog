<?php

declare(strict_types=1);

namespace App\View;

use App\Domain\Entity\Comment;

class CommentView
{
    /**
     * @param Comment $comment
     *
     * @return array{
     *  guid: string,
     *  createdAt: string,
     *  comment: string
     * }
     */
    public static function create(Comment $comment): array
    {
        assert($comment instanceof Comment);

        return [
            'guid'      => $comment->guid(),
            'createdAt' => DateTimeView::create($comment->createdAt()),
            'comment'   => $comment->text(),
            'repliedTo' => $comment->repliedTo(),
        ];
    }
}
