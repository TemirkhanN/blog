<?php

declare(strict_types=1);

namespace App\Entity;

class Post
{
    private $id;

    private $title;

    private $slug;

    private $content;

    private $author;

    public function __construct(string $id, string $title, string $content, string $author)
    {
        $this->id      = $id;
        $this->title   = $title;
        $this->content = $content;
        $this->slug    = preg_replace('#\W#', '', $title);
        $this->author  = $author;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
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
