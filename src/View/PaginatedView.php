<?php

declare(strict_types=1);

namespace App\View;

use App\Service\Response\ValueObject\CollectionChunk;
use Temirkhan\View\AbstractAggregateView;

class PaginatedView extends AbstractAggregateView
{
    public function getView($context)
    {
        if (!$context[1] instanceof CollectionChunk) {
            return null;
        }

        $collectionChunk = $context[1];

        $data = [];
        foreach ($collectionChunk->chunk as $item) {
            $data[] = $this->createView($context[0], $item);
        }

        return [
            'data'       => $data,
            'pagination' => $this->pagination($collectionChunk),
        ];
    }

    private function pagination(CollectionChunk $context): array
    {
        return [
            'limit'  => $context->limit,
            'offset' => $context->offset,
            'total'  => $context->ofTotalAmount,
        ];
    }
}
