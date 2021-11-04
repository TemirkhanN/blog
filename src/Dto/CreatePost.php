<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Vseinstrumentiru\DataObjectBundle\Object\AbstractObject;

/**
 * Post creation data transfer object.
 */
class CreatePost extends AbstractObject
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
