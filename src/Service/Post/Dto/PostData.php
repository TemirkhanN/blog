<?php

declare(strict_types=1);

namespace App\Service\Post\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use ViTech\DataObjectBundle\Object\AbstractObject;

/**
 * Post creation data transfer object.
 */
class PostData extends AbstractObject
{
    #[Assert\NotBlank]
    public string $title;

    #[Assert\NotBlank]
    public string $preview;

    #[Assert\NotBlank]
    public string $content;

    /**
     * @var string[]
     */
    #[Assert\All([new Assert\NotBlank(), new Assert\Type('alnum')])]
    public array $tags;
}
