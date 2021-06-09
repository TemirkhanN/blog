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
     * @Assert\NotBlank()
     */
    public string $title;

    /**
     * @Assert\NotBlank()
     */
    public string $preview;

    /**
     * @Assert\NotBlank()
     */
    public string $content;
}
