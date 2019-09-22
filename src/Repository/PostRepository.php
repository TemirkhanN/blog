<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostCollection;
use Redis;

class PostRepository implements PostRepositoryInterface
{
    /**
     * Hash scope in redis
     *
     * @const string
     */
    private const TABLE_NAME = 'blog_posts';

    /**
     * @var Redis
     */
    private $storage;

    /**
     * Constructor
     *
     * @param Redis $redis
     */
    public function __construct(Redis $redis)
    {
        $this->storage = $redis;
    }

    public function getPosts(int $limit, int $offset): PostCollection
    {
        return new PostCollection(
            (function (int $limit, int $offset) {
                if ($offset < 0 || $offset > $this->storage->hLen(self::TABLE_NAME)) {
                    return;
                }

                foreach ($this->storage->hGetAll(self::TABLE_NAME) as $post) {
                    if ($offset-- > 0) {
                        continue;
                    }

                    if ($limit-- === 0) {
                        break;
                    }

                    yield unserialize($post, [Post::class]);
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
        $iterator = null;
        $posts = $this->storage->hScan(self::TABLE_NAME, $iterator, "*_$slug");
        if ($posts === []) {
            return null;
        }

        return unserialize(reset($posts), [Post::class]);
    }

    /**
     * @param Post $post
     */
    public function save(Post $post): void
    {
        $hash = sprintf('%s_%s', $post->getId(), $post->getSlug());

        $this->storage->hSet(self::TABLE_NAME, $hash, serialize($post));
    }
}
