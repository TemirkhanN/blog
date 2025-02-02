<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NewComment
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(
            min: 30,
            max: 4000,
            minMessage: 'Meaningful comment shall contain at least 5-6 words.',
            maxMessage: '4000 symbols is an amount of an average post. Please, have some mercy, shorten your comment.'
        )]
        public readonly string $text
    ) {
    }
}
