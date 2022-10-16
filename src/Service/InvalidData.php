<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use TemirkhanN\Generic\Error;

class InvalidData extends Error
{
    public static function fromConstraintsViolation(ConstraintViolationListInterface $violations): Error
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return new self('Invalid data', 0, $errors);
    }
}
