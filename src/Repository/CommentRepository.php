<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use TemirkhanN\Generic\Collection\Collection;
use TemirkhanN\Generic\Collection\CollectionInterface;

class CommentRepository implements CommentRepositoryInterface
{
    private static ?ObjectManager $entityManager = null;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        self::init($managerRegistry);
    }

    public static function init(ManagerRegistry $registry): void
    {
        self::$entityManager = $registry->getManagerForClass(Comment::class);
    }

    public static function save(Comment $comment): void
    {
        $em = self::entityManager();

        $em->persist($comment);
        $em->flush();
    }

    public function findCommentByGuid(string $guid): ?Comment
    {
        return self::entityManager()->find(Comment::class, $guid);
    }

    public function countCommentsInInterval(DateInterval $interval): int
    {
        // @phpstan-ignore-next-line
        return (int) $this->createQueryBuilder()
            ->select('COUNT(c)')
            ->from(Comment::class, 'c')
            ->where('c.createdAt > :fromTime')
            ->setParameters(['fromTime' => (new DateTime())->sub($interval)])
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Post $post
     *
     * @return CollectionInterface<Comment>
     */
    public function findCommentsByPost(Post $post): CollectionInterface
    {
        /** @var iterable<Comment> $comments */
        $comments = $this->createQueryBuilder()
                         ->select('c')
                         ->from(Comment::class, 'c')
                         ->where('c.post = :post')
                         ->setParameters(['post' => $post])
                         ->orderBy('c.createdAt', 'DESC')
                         ->getQuery()
                         ->toIterable();

        return new Collection($comments);
    }

    private static function createQueryBuilder(): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = self::entityManager();

        return $em->createQueryBuilder();
    }

    private static function entityManager(): ObjectManager
    {
        if (self::$entityManager === null) {
            throw new RuntimeException('Repository has to be initialized before accessing static calls');
        }

        return self::$entityManager;
    }
}
