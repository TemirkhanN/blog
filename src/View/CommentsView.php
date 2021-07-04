<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Collection;
use App\Entity\Comment;
use DateTimeInterface;
use Temirkhan\View\ViewInterface;

class CommentsView implements ViewInterface
{
    /**
     * @param mixed $context
     *
     * @return array{
     *  guid: string,
     *  creationDate: string,
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
            $comments[] = $item;

            $repliedTo = $item->getRepliedCommentGuid();
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
     *  creationDate: string,
     *  comment: string,
     *  replies: array
     * }
     */
    public function createCommentView(Comment $comment, array &$allReplies): array
    {
        $view = [
            'guid'         => $comment->getGuid(),
            'creationDate' => $comment->getCreationDate()->format(DateTimeInterface::ATOM),
            'comment'      => $comment->getComment(),
            'replies'      => [],
        ];
        if (!isset($allReplies[$comment->getGuid()])) {
            return $view;
        }

        $replies = $allReplies[$comment->getGuid()];
        unset($allReplies[$comment->getGuid()]);
        foreach ($replies as $reply) {
            $view['replies'][] = $this->createCommentView($reply, $allReplies);
        }

        return $view;
    }
}
