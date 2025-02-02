<?php

declare(strict_types=1);

namespace App\View;

use App\Domain\Entity\Comment;
use TemirkhanN\Generic\Collection\CollectionInterface;

class CommentsTreeView
{
    /**
     * @param CollectionInterface<Comment> $comments
     *
     * @return array{
     *  guid: string,
     *  createdAt: string,
     *  comment: string,
     *  replies: array<mixed>
     * }[]
     */
    public static function create(CollectionInterface $comments): array
    {
        $rootComments = [];
        $replies      = [];
        foreach ($comments as $item) {
            $repliedTo = $item->repliedTo();
            if ($repliedTo !== null) {
                $replies[$repliedTo][] = $item;
            } else {
                $rootComments[] = $item;
            }
        }

        $view = [];
        foreach ($rootComments as $comment) {
            $view[] = self::createCommentView($comment, $replies);
        }

        return $view;
    }

    /**
     * @param Comment                  $comment
     * @param array<string, Comment[]> $allReplies
     *
     * @return array{
     *  guid: string,
     *  createdAt: string,
     *  comment: string,
     *  replies: array<mixed>
     * }
     */
    private static function createCommentView(Comment $comment, array &$allReplies): array
    {
        $view            = CommentView::create($comment);
        $view['replies'] = [];

        if (!isset($allReplies[$comment->guid()])) {
            return $view;
        }

        $replies = $allReplies[$comment->guid()];
        unset($allReplies[$comment->guid()]);
        foreach ($replies as $reply) {
            $view['replies'][] = self::createCommentView($reply, $allReplies);
        }

        return $view;
    }
}
