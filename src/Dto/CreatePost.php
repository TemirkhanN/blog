<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post creation data transfer object.
 * It looks strange that validation is made in controller based on constraints in DTO. It wouldn't be that strange if
 * both of them belong to application layer(for now DTO is passed to domain layer too)
 */
class CreatePost
{
    public $title;

    public $content;

    /**
     * TODO move to yaml in application layer
     *
     * @return Assert\Collection
     */
    public static function getConstraints(): Assert\Collection
    {
        return new Assert\Collection(
            [
                'allowExtraFields'   => true,
                'allowMissingFields' => false,
                'fields'             => [
                    'title'   => new Assert\NotBlank(),
                    'content' => new Assert\NotBlank(),
                ],
            ]
        );
    }

    public function __construct(array $data)
    {
        $this->content = $data['content'];
        $this->title   = $data['title'];
    }
}
