<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Service\Post\Dto\PostFilter;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use TemirkhanN\Generic\Collection\Collection;
use TemirkhanN\Generic\Collection\CollectionInterface;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @param PostFilter $filter
     *
     * @return CollectionInterface<Post>
     */
    public static function getPosts(PostFilter $filter): CollectionInterface
    {
        $query = ORM::instance()->createQueryBuilder()
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
            // HIDDEN keyword removes a following field from the result
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

    public static function countPosts(PostFilter $filter): int
    {
        $query = ORM::instance()->createQueryBuilder()
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

    public static function findOneBySlug(string $slug): ?Post
    {
        /** @var ?Post $result */
        $result = ORM::instance()->getRepository(Post::class)->findOneBy(['slug' => $slug]);

        return $result;
    }

    public static function save(Post $post): void
    {
        ORM::instance()->persist($post);
    }
}
