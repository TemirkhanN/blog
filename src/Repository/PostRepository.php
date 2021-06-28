<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * Post repository
 *
 * @implements PostRepositoryInterface<Post>
 */
class PostRepository implements PostRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getPosts(int $limit, int $offset): Collection
    {
        assert($limit > 0);
        assert($offset >= 0);

        // TODO multi-queries shall be replaced with something better. At worst scenario - cache it
        return new Collection(
            (function (int $limit, int $offset) {
                return new Paginator($this->createQueryBuilder()
                                ->addSelect('p', 't')
                                ->from(Post::class, 'p')
                                ->leftJoin('p.tags', 't')
                                ->orderBy('p.publishedAt', 'DESC')
                                ->setMaxResults($limit)
                                ->setFirstResult($offset)
                                ->getQuery());
            })($limit, $offset)
        );
    }

    public function findPostsByTag(string $tag, int $limit, int $offset): Collection
    {
        assert($limit > 0);
        assert($offset >= 0);

        return new Collection(
            (function (string $tag, int $limit, int $offset) {
                return new Paginator($this->createQueryBuilder()
                                ->select('p')
                                ->addSelect('t')
                                ->from(Post::class, 'p')
                                ->leftJoin('p.tags', 't')
                                ->andWhere(':tag MEMBER OF p.tags')
                                ->setParameters(['tag' => $tag])
                                ->orderBy('p.publishedAt', 'DESC')
                                ->setMaxResults($limit)
                                ->setFirstResult($offset)
                                ->getQuery());
            })($tag, $limit, $offset)
        );
    }

    public function countPosts(): int
    {
        return (int) $this->createQueryBuilder()
                         ->select('COUNT(p)')
                         ->from(Post::class, 'p')
                         ->getQuery()->getSingleScalarResult();
    }

    public function countPostsByTag(string $tag): int
    {
        return (int) $this->createQueryBuilder()
                         ->select('COUNT(p)')
                         ->from(Post::class, 'p')
                         ->innerJoin('p.tags', 't', Join::WITH, 't.name=:tag')
                         ->setParameters(['tag' => $tag])
                         ->getQuery()->getSingleScalarResult();
    }

    public function findOneBySlug(string $slug): ?Post
    {
        return $this->registry->getRepository(Post::class)->findOneBy(['slug' => $slug]);
    }

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
