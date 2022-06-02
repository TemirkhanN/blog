<?php

declare(strict_types=1);

namespace App\Service\Post\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Vseinstrumentiru\DataObjectBundle\Object\AbstractObject;

/**
 * Post creation data transfer object.
 *
 * @uses Assert
 */
class PostData extends AbstractObject
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

    /**
     * @var string[]
     *
     * @Assert\All({
     * @Assert\NotBlank(),
     * @Assert\Type("alnum")
     * })
     */
    public array $tags;
}