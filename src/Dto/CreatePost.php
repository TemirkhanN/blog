<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post creation data transfer object.
 */
class CreatePost extends DataTransferObject
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public string $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public string $preview;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public string $content;
}
