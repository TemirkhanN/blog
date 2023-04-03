<?php

declare(strict_types=1);

namespace Frontend\API;

class Metadata
{
    public readonly int $page;

    public readonly ?int $nextPage;

    public function __construct(public readonly int $limit, public readonly int $offset, public readonly int $ofTotal)
    {
        $page = 1;
        if ($this->offset > 0) {
            $page = floor($this->offset/$this->limit) + 1;
        }
        $this->page = (int)$page;

        $nextPage = null;
        if ($this->ofTotal > $this->offset + $this->limit) {
            $nextPage = $this->page + 1;
        }
        $this->nextPage = $nextPage;
    }
}



