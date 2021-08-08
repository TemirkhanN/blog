<?php

declare(strict_types=1);

namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FunctionalTestCase extends KernelTestCase
{
    /** @var array<ClassMetadata<object>> */
    private static array $cachedMetadata = [];

    private ManagerRegistry $doctrineRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        /** @var ManagerRegistry $doctrineRegistry */
        $doctrineRegistry       = self::$kernel->getContainer()->get('doctrine');
        $this->doctrineRegistry = $doctrineRegistry;

        $schema = new SchemaTool($this->getEntityManager());

        if (static::$cachedMetadata === []) {
            static::$cachedMetadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
        }

        $schema->createSchema(static::$cachedMetadata);
    }

    protected function tearDown(): void
    {
        $schema = new SchemaTool($this->getEntityManager());
        $schema->dropSchema(static::$cachedMetadata);

        parent::tearDown();
    }

    final protected function getDoctrineRegistry(): ManagerRegistry
    {
        return $this->doctrineRegistry;
    }

    final protected function getEntityManager(): EntityManager
    {
        /** @var EntityManager $manager */
        $manager = $this->doctrineRegistry->getManager();

        return $manager;
    }
}
