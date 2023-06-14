<?php

declare(strict_types=1);

namespace App\Entity;

class Tag
{
    private string $name;

    private Post $post;

    public function __construct(string $name, Post $post)
    {
        $this->name = $name;
        $this->post = $post;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name();
    }
}
