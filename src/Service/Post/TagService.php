<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Comment;
use App\Entity\Tag;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use RuntimeException;

class TagService
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string[] $names
     *
     * @return iterable<Tag>
     */
    public function createTags(array $names): iterable
    {
        $repository = $this->getRepository();

        /** @var Tag[] $existingTags */
        $existingTags = $repository->findBy(['name' => $names]);

        $existingTagsNames = [];
        foreach ($existingTags as $tag) {
            $existingTagsNames[] = $tag->name();
        }

        $em = $this->getEntityManager();

        $newTags = [];
        foreach ($names as $name) {
            if (!in_array($name, $existingTagsNames, true)) {
                $newTag    = new Tag($name);
                $newTags[] = $newTag;

                $em->persist($newTag);
            }
        }

        if ($newTags !== []) {
            $em->flush();
        }

        yield from $existingTags;

        yield from $newTags;
    }

    public function createTag(string $name): Tag
    {
        $repository  = $this->getRepository();
        $existingTag = $repository->find($name);
        if ($existingTag !== null) {
            return $existingTag;
        }

        $newTag = new Tag($name);
        $em     = $this->getEntityManager();
        $em->persist($newTag);
        $em->flush();

        return $newTag;
    }

    /**
     * @return ObjectRepository<Tag>
     */
    private function getRepository(): ObjectRepository
    {
        $em = $this->getEntityManager();

        return $em->getRepository(Tag::class);
    }

    private function getEntityManager(): ObjectManager
    {
        $em = $this->registry->getManagerForClass(Comment::class);
        if ($em === null) {
            throw new RuntimeException('No relation configured to handle Tag entity');
        }

        return $em;
    }
}
