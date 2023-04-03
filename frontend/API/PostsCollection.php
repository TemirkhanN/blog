<?php

declare(strict_types=1);

namespace Frontend\API;

use Traversable;

class PostsCollection implements \IteratorAggregate
{
    public function __construct(public readonly array $posts, public readonly Metadata $metadata)
    {
    }

    public function getIterator(): Traversable
    {
        yield from $this->posts;
    }
}
