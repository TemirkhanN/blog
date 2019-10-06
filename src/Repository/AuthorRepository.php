<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Redis;

class AuthorRepository implements AuthorRepositoryInterface
{
    /**
     * Hash scope in redis
     *
     * @const string
     */
    private const TABLE_NAME = 'blog_authors';

    /**
     * Data storage
     *
     * @var Redis
     */
    private $storage;

    /**
     * Constructor
     *
     * @param array $initialAuthors
     * @param Redis $redis
     */
    public function __construct(array $initialAuthors, Redis $redis)
    {
        $this->storage = $redis;

        foreach ($initialAuthors as $author) {
            $data = serialize(new Author($author['name']));

            $this->storage->hSet(self::TABLE_NAME, $author['name'], $data);
        }
    }

    public function findByName(string $name): ?Author
    {
        $authorData = $this->storage->hGet(self::TABLE_NAME, $name);
        if (!$authorData) {
            return null;
        }

        return unserialize($authorData, [Author::class]);
    }
}
