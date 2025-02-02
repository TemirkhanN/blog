<?php

declare(strict_types=1);

namespace App\Domain\Repository;

class PostFilter
{
    public ?int $limit         = null;
    public int $offset         = 0;
    public ?string $tag        = null;
    public bool $onlyPublished = true;

    public static function create(
        ?int $limit = null,
        int $offset = 0,
        ?string $tag = null,
        bool $onlyPublished = true
    ): self {
        $filter                = new self();
        $filter->limit         = $limit;
        $filter->offset        = $offset;
        $filter->tag           = $tag;
        $filter->onlyPublished = $onlyPublished;

        return $filter;
    }
}
