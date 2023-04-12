<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Service\Post\Dto\PostFilter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use TemirkhanN\Generic\Collection\Collection;
use TemirkhanN\Generic\Collection\CollectionInterface;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    /**
     * @param PostFilter $filter
     *
     * @return CollectionInterface<Post>
     */
    public function getPosts(PostFilter $filter): CollectionInterface
    {
        $query = $this->createQueryBuilder()
                      ->addSelect('p', 'pt')
                      ->from(Post::class, 'p');

        if ($filter->tag !== null) {
            $query->innerJoin('p.tags', 't', Join::WITH, 't.name=:tag');
            $query->setParameter('tag', $filter->tag);
        }

        $query->leftJoin('p.tags', 'pt');

        if ($filter->onlyPublished) {
            $query->andWhere('p.state = :state');
            $query->setParameter('state', Post::STATE_PUBLISHED);
            $query->addOrderBy('p.publishedAt', 'DESC');
        } else {
            // HIDDEN keyword removes following field from the result
            $query->addSelect('COALESCE(p.publishedAt, p.updatedAt, p.createdAt) as HIDDEN modifiedDate');
            $query->addOrderBy('modifiedDate', 'DESC');
        }

        if ($filter->limit !== null) {
            $query->setMaxResults($filter->limit);
        }

        if ($filter->offset !== 0) {
            $query->setFirstResult($filter->offset);
        }

        return new Collection(new Paginator($query));
    }

    public function countPosts(PostFilter $filter): int
    {
        $query = $this->createQueryBuilder()
                      ->addSelect('COUNT(p)')
                      ->from(Post::class, 'p');

        if ($filter->tag !== null) {
            $query->innerJoin('p.tags', 't', Join::WITH, 't.name=:tag');
            $query->setParameter('tag', $filter->tag);
        }

        if ($filter->onlyPublished) {
            $query->andWhere('p.state = :state');
            $query->setParameter('state', Post::STATE_PUBLISHED);
        }

        // @phpstan-ignore-next-line
        return (int) $query->getQuery()->getSingleScalarResult();
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
