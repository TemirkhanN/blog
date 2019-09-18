<?php
declare(strict_types=1);

namespace App\Entity;

use Iterator;
use IteratorAggregate;

abstract class AbstractCollection implements IteratorAggregate
{
    /**
     * @var iterable
     */
    private $items;

    final public function __construct(iterable $items)
    {
        $this->items = $items;
    }

    /**
     * @return Iterator
     */
    final public function getIterator()
    {
        yield from $this->getItems();
    }

    /**
     * @return iterable
     */
    final public function getItems(): iterable
    {
        yield from $this->items;
    }
}
