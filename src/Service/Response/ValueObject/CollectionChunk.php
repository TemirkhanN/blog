<?php

declare(strict_types=1);

namespace App\Service\Response\ValueObject;

class CollectionChunk
{
    public int $limit;

    public int $offset;

    public int $ofTotalAmount;

    public iterable $chunk;

    public function __construct(int $limit, int $offset, int $ofTotalAmount, iterable $chunk)
    {
        $this->limit         = $limit;
        $this->offset        = $offset;
        $this->ofTotalAmount = $ofTotalAmount;
        $this->chunk         = $chunk;
    }
}
