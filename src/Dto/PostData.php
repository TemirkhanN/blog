<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PostData
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $title,
        #[Assert\NotBlank]
        public readonly string $preview,
        #[Assert\NotBlank]
        public readonly string $content,
        /**
         * @var string[]
         */
        #[Assert\All([new Assert\NotBlank(), new Assert\Type('alnum')])]
        public readonly array $tags = [],
    ) {
    }
}
