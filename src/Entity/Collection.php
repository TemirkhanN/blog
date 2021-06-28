<?php

declare(strict_types=1);

namespace App\Entity;

use IteratorAggregate;
use Traversable;

/**
 * @template   T
 * @implements IteratorAggregate<T>
 */
class Collection implements IteratorAggregate
{
    /** @var iterable<T> */
    private iterable $items;

    /** @param iterable<T> $items */
    final public function __construct(iterable $items)
    {
        $this->items = $items;
    }

    /**
     * @return Traversable<T>
     */
    final public function getIterator()
    {
        yield from $this->items;
    }
}
