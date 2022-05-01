<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Comment;
use Temirkhan\View\ViewInterface;

class CommentView implements ViewInterface
{
    /**
     * @param mixed $context
     *
     * @return array{
     *  guid: string,
     *  createdAt: string,
     *  comment: string
     * }
     */
    public function getView($context)
    {
        assert($context instanceof Comment);

        return [
            'guid'      => $context->guid(),
            'createdAt' => $context->createdAt()->format(DATE_W3C),
            'comment'   => $context->text(),
        ];
    }
}
