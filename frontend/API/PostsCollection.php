<?php

declare(strict_types=1);

namespace Frontend\API;

use Frontend\API\Model\Post;
use IteratorAggregate;
use Traversable;

/**
 * @template-covariant Post
 * @extends            IteratorAggregate<Post>
 */
readonly class PostsCollection implements IteratorAggregate
{
    /**
     * @param Post[]   $posts
     * @param Metadata $metadata
     */
    public function __construct(public array $posts, public Metadata $metadata)
    {
    }

    public function count(): int
    {
        return count($this->posts);
    }

    /**
     * @return Traversable<Post>
     */
    public function getIterator(): Traversable
    {
        yield from $this->posts;
    }
}
