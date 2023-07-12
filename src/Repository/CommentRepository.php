<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use Carbon\Carbon;
use DateInterval;
use Doctrine\ORM\QueryBuilder;
use TemirkhanN\Generic\Collection\Collection;
use TemirkhanN\Generic\Collection\CollectionInterface;

class CommentRepository implements CommentRepositoryInterface
{
    public static function save(Comment $comment): void
    {
        ORM::instance()->persist($comment);
    }

    public function findCommentByGuid(string $guid): ?Comment
    {
        return ORM::instance()->getRepository(Comment::class)->find($guid);
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

    private static function createQueryBuilder(): QueryBuilder
    {
        return ORM::instance()->createQueryBuilder();
    }
}
