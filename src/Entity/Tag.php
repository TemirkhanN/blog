<?php

declare(strict_types=1);

namespace App\Entity;

class Tag
{
    /**
     * @var int
     *
     * @phpstan-ignore-next-line
     */
    private int $id;

    private string $name;

    /**
     * @phpstan-ignore-next-line
     */
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
