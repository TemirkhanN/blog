<?php

declare(strict_types=1);

namespace App\Service\Response\ValueObject;

class CollectionChunk
{
    /** @var int */
    public $limit;
    /** @var int */
    public $offset;
    /** @var int */
    public $ofTotalAmount;
    /** @var iterable */
    public $chunk;

    public function __construct(int $limit, int $offset, int $ofTotalAmount, iterable $chunk)
    {
        $this->limit         = $limit;
        $this->offset        = $offset;
        $this->ofTotalAmount = $ofTotalAmount;
        $this->chunk         = $chunk;
    }
}