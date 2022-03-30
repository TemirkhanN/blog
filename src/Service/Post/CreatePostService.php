<?php

declare(strict_types=1);

namespace App\Service\Post;

use App\Dto\CreatePost;
use App\Entity\Post;
use App\Repository\PostRepositoryInterface;

class CreatePostService
{
    /** @var PostRepositoryInterface<Post> */
    private PostRepositoryInterface $repository;

    private TagService $tagService;

    /**
     * @param PostRepositoryInterface<Post> $postRepository
     * @param TagService                    $tagService
     */
    public function __construct(PostRepositoryInterface $postRepository, TagService $tagService)
    {
        $this->repository = $postRepository;
        $this->tagService = $tagService;
    }

    /**
     * @param CreatePost $data
     *
     * @return Post
     *
     * @throws \DomainException
     */
    public function execute(CreatePost $data): Post
    {
        $slug = sprintf(
            '%s_%s',
            date('Y-m-d'),
            (string) preg_replace('#\W#u', '-', $data->title)
        );

        if ($this->repository->findOneBySlug($slug)) {
            throw new \DomainException('There already exists the post with similar title');
        }

        $post = new Post($slug, $data->title, $data->preview, $data->content);

        foreach ($this->tagService->createTags($data->tags) as $tag) {
            $post->addTag($tag);
        }

        $this->repository->save($post);

        return $post;
    }
}
