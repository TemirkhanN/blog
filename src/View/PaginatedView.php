<?php

declare(strict_types=1);

namespace App\View;

use App\Service\Response\Dto\CollectionChunk;
use Temirkhan\View\AbstractAggregateView;

class PaginatedView extends AbstractAggregateView
{
    /**
     * @param mixed $context
     *
     * @return null|array{
     *  data: mixed,
     *  pagination: array{
     *      limit: int,
     *      offset: int,
     *      total: int
     *  }
     * }
     */
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

    /**
     * @param CollectionChunk<object> $context
     *
     * @return array{
     *  limit: int,
     *  offset: int,
     *  total: int
     * }
     */
    private function pagination(CollectionChunk $context): array
    {
        return [
            'limit'  => $context->limit,
            'offset' => $context->offset,
            'total'  => $context->ofTotalAmount,
        ];
    }
}
