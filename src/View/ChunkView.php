<?php

declare(strict_types=1);

namespace App\View;

use Temirkhan\View\AbstractAggregateView;

class ChunkView extends AbstractAggregateView
{
    public function getView($context)
    {
        return [
            'items'       => $this->createView($context[0], $context[1]),
            'totalAmount' => $context[2],
        ];
    }
}