<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostCollection;
use SplObjectStorage;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var Post[]
     */
    private $storage;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();

        $posts = [
            new Post(3, 'Fixture attacks!', 'Fixture content', 'Me'),
            new Post(2, 'Another title', 'Fixture content', 'Some author'),
            new Post(1, 'Some title', 'Fixture content', 'Some author'),
        ];

        foreach ($posts as $fixture) {
            $this->storage->attach($fixture);
        }
    }

    public function getPosts(int $limit, int $offset): PostCollection
    {
        return new PostCollection(
            (function (int $limit, int $offset) {
                if ($offset < 0 || $offset > $this->storage->count()) {
                    return;
                }

                foreach ($this->storage as $post) {
                    if ($offset-- > 0) {
                        continue;
                    }

                    if ($limit-- === 0) {
                        break;
                    }

                    yield $post;
                }
            })(
                $limit, $offset
            )
        );
    }

    /**
     * @param string $slug
     *
     * @return Post|null
     */
    public function findOneBySlug(string $slug): ?Post
    {
        foreach ($this->storage as $item) {
            if ($item->getSlug() === $slug) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param Post $post
     */
    public function save(Post $post): void
    {
        if ($this->storage->contains($post)) {
            return;
        }

        $this->storage->attach($post);
    }
}
