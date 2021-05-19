<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * Post repository
 */
class PostRepository implements PostRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getPosts(int $limit, int $offset): PostCollection
    {
        return new PostCollection(
            (function (int $limit, int $offset) {
                yield from $this->registry->getRepository(Post::class)->findBy([], [], $limit, $offset);
            })($limit, $offset)
        );
    }

    public function getPostsByTag(string $tag, int $limit, int $offset): PostCollection
    {
        return new PostCollection(
            (function (string $tag, int $limit, int $offset) {
                return $this->createQueryBuilder()->select('p')
                            ->from(Post::class, 'p')
                            ->innerJoin('p.tags', 't', Join::WITH, 't.name=:tag')
                            ->setParameters(['tag' => $tag])
                            ->orderBy('p.publishedAt', 'DESC')
                            ->setMaxResults($limit)
                            ->setFirstResult($offset)
                            ->getQuery()->getResult();
            })($tag, $limit, $offset)
        );
    }

    /**
     * Finds post by slug
     *
     * @param string $slug
     *
     * @return Post|null
     */
    public function findOneBySlug(string $slug): ?Post
    {
        return $this->registry->getRepository(Post::class)->findOneBy(['slug' => $slug]);
    }

    /**
     * Saves post in repository
     *
     * @param Post $post
     */
    public function save(Post $post): void
    {
        $em = $this->registry->getManagerForClass(Post::class);
        if ($em === null) {
            throw new RuntimeException('No relation configured to handle Post entity');
        }

        $em->persist($post);
        $em->flush();
    }

    private function createQueryBuilder(): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        return $em->createQueryBuilder();
    }
}
