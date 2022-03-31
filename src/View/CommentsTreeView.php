<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Collection;
use App\Entity\Comment;
use Temirkhan\View\AbstractAggregateView;

class CommentsTreeView extends AbstractAggregateView
{
    /**
     * @param mixed $context
     *
     * @return array{
     *  guid: string,
     *  createdAt: string,
     *  comment: string,
     *  replies: array
     * }[]
     */
    public function getView($context)
    {
        assert($context instanceof Collection);

        $rootComments = [];
        $replies      = [];
        foreach ($context as $item) {
            assert($item instanceof Comment);

            $repliedTo = $item->repliedTo();
            if ($repliedTo !== null) {
                $replies[$repliedTo][] = $item;
            } else {
                $rootComments[] = $item;
            }
        }

        $view = [];
        foreach ($rootComments as $comment) {
            $view[] = $this->createCommentView($comment, $replies);
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
     *  replies: array
     * }
     */
    public function createCommentView(Comment $comment, array &$allReplies): array
    {
        $view            = $this->createView('comment', $comment);
        $view['replies'] = [];

        if (!isset($allReplies[$comment->guid()])) {
            return $view;
        }

        $replies = $allReplies[$comment->guid()];
        unset($allReplies[$comment->guid()]);
        foreach ($replies as $reply) {
            $view['replies'][] = $this->createCommentView($reply, $allReplies);
        }

        return $view;
    }
}
