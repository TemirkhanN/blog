<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use RuntimeException;

final class ORM
{
    private static ?self $instance = null;

    private bool $hasPerformedChanges = false;

    private function __construct(private readonly ManagerRegistry $registry)
    {
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new RuntimeException('Attempted to unserialize a singleton.');
    }

    public static function init(ManagerRegistry $doctrine): void
    {
        self::$instance = new self($doctrine);
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            throw new RuntimeException('ORM has to be initialized before use.');
        }

        return self::$instance;
    }

    public function persist(object $object): void
    {
        $this->hasPerformedChanges = true;

        $this->registry->getManager()->persist($object);
    }

    /**
     * @param class-string<T> $forEntityClass
     *
     * @return ObjectRepository<T>
     *
     * @template T of object
     */
    public function getRepository(string $forEntityClass): ObjectRepository
    {
        return $this->registry->getRepository($forEntityClass);
    }

    public function createQueryBuilder(): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        return $em->createQueryBuilder();
    }

    public function saveChanges(): void
    {
        if (!$this->hasPerformedChanges) {
            return;
        }

        $this->registry->getManager()->flush();
        $this->hasPerformedChanges = false;
    }
}
