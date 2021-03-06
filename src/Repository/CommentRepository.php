<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Comment;
use App\Entity\Post;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;

class CommentRepository implements CommentRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
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

    public function countCommentsInLastInterval(DateInterval $interval): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(c)')
            ->from(Comment::class, 'c')
            ->where('c.createdAt > :fromTime')
            ->setParameters(['fromTime' => (new DateTime())->sub($interval)])
            ->getQuery()->getSingleScalarResult();
    }

    public function findCommentsByPost(Post $post): Collection
    {
        return new Collection(
            (function (Post $post) {
                yield from $this->createQueryBuilder()
                    ->select('c')
                    ->from(Comment::class, 'c')
                    ->where('c.post = :post')
                    ->setParameters(['post' => $post])
                    ->orderBy('c.createdAt', 'DESC')
                    ->getQuery()
                    ->getResult();
            })($post)
        );
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
