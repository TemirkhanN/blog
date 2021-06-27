<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;

/**
 * @template C of Comment
 */
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

    private function getEntityManager(): ObjectManager
    {
        $em = $this->registry->getManagerForClass(Comment::class);
        if ($em === null) {
            throw new RuntimeException('No relation configured to handle Comment entity');
        }

        return $em;
    }
}
