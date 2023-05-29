<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\PostRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use RuntimeException;

class TagService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly ManagerRegistry         $registry
    )
    {
    }

    /**
     * @param Post $post
     * @param string[] $tags
     */
    public function addTags(Post $post, array $tags): void
    {
        $repository = $this->getRepository();

        /** @var Tag[] $existingTags */
        $existingTags = $repository->findBy(['name' => $tags]);
        $existingTagsNames = [];
        foreach ($existingTags as $tag) {
            $existingTagsNames[] = $tag->name();
            $post->addTag($tag);
        }

        foreach ($tags as $name) {
            if (!in_array($name, $existingTagsNames, true)) {
                $post->addTag(new Tag($name));
            }
        }

        $this->postRepository->save($post);
    }

    /**
     * @return ObjectRepository<Tag>
     */
    private function getRepository(): ObjectRepository
    {
        $em = $this->registry->getManagerForClass(Tag::class);
        if ($em === null) {
            throw new RuntimeException('No relation configured to handle Tag entity');
        }

        return $em->getRepository(Tag::class);
    }
}
