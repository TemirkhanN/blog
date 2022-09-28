<?php

declare(strict_types=1);

namespace App\Service\Post\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use ViTech\DataObjectBundle\Object\AbstractObject;

/**
 * @uses Assert
 */
class NewComment extends AbstractObject
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=30,
     *     max=4000,
     *     minMessage="Meaningfull comment shall contain at least 5-6 words.",
     *     maxMessage="4000 symbols is an amount of an average post. Please, have some mercy, shorten your comment."
     * )
     */
    public string $text;
}
