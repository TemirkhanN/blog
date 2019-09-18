<?php

declare(strict_types=1);

namespace App\Entity;

class Post
{
    private $id;

    private $title;

    private $slug;

    private $author;

    public function __construct(int $id, string $title, string $author)
    {
        $this->id     = $id;
        $this->title  = $title;
        $this->slug   = preg_replace('#\W#', '', $title);
        $this->author = $author;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
