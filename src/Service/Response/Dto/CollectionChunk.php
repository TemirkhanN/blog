<?php

declare(strict_types=1);

namespace App\Service\Response\Dto;

/**
 * @template-covariant T
 */
class CollectionChunk
{
    public int $limit;

    public int $offset;

    public int $ofTotalAmount;

    /**
     * @var iterable<T>
     */
    public iterable $chunk;

    /**
     * @param int         $limit
     * @param int         $offset
     * @param int         $ofTotalAmount
     * @param iterable<T> $chunk
     */
    public function __construct(int $limit, int $offset, int $ofTotalAmount, iterable $chunk)
    {
        $this->limit         = $limit;
        $this->offset        = $offset;
        $this->ofTotalAmount = $ofTotalAmount;
        $this->chunk         = $chunk;
    }
}
