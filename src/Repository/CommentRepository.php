<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use Carbon\Carbon;
use DateInterval;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use TemirkhanN\Generic\Collection\Collection;
use TemirkhanN\Generic\Collection\CollectionInterface;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function save(Comment $comment): void
    {
        $em = $this->getEntityManager();

        $em->persist($comment);
        $em->flush();
    }

    public function findCommentByGuid(string $guid): ?Comment
    {
        return $this->getEntityManager()->find(Comment::class, $guid);
    }

    public function countCommentsInInterval(DateInterval $interval): int
    {
        // @phpstan-ignore-next-line
        return (int) $this->createQueryBuilder()
            ->select('COUNT(c)')
            ->from(Comment::class, 'c')
            ->where('c.createdAt > :fromTime')
            ->setParameters(['fromTime' => Carbon::now()->sub($interval)])
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

    private function getEntityManager(): ObjectManager
    {
        $em = $this->registry->getManagerForClass(Comment::class);
        if ($em === null) {
            throw new RuntimeException('No relation configured to handle Comment entity');
        }

        return $em;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();

        return $em->createQueryBuilder();
    }
}
