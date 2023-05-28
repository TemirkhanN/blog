<?php

namespace App\Service\Post\Dto;

class InvalidInput extends \RuntimeException
{
    public function __construct(private readonly array $details)
    {
        parent::__construct('Invalid data', 0);
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
