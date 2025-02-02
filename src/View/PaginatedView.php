<?php

declare(strict_types=1);

namespace App\View;

use App\Lib\Response\Payload\CollectionChunk;

class PaginatedView
{
    /**
     * @param CollectionChunk<mixed> $collection
     * @param callable               $view
     *
     * @return array{
     *  data: mixed,
     *  pagination: array{
     *      limit: int,
     *      offset: int,
     *      total: int
     *  }
     * }
     */
    public static function create(CollectionChunk $collection, callable $view): array
    {
        $data = [];
        foreach ($collection->chunk as $item) {
            $data[] = $view($item);
        }

        return [
            'data'       => $data,
            'pagination' => [
                'limit'  => $collection->limit,
                'offset' => $collection->offset,
                'total'  => $collection->ofTotalAmount,
            ],
        ];
    }
}
