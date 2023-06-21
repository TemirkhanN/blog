<?php

declare(strict_types=1);

namespace App\View;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorsView
{
    public static function create(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return [
            'message' => 'Invalid data',
            'code'    => 0,
            'details' => $errors,
        ];
    }
}
