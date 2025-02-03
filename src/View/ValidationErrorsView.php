<?php

declare(strict_types=1);

namespace App\View;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorsView
{
    public static function create(ConstraintViolationListInterface $violations, int $code = 0): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return [
            'message' => 'Invalid data',
            'code'    => $code,
            'details' => $errors,
        ];
    }

    public static function createPlain(array $violations, int $code = 0): array
    {
        return [
            'message' => 'Invalid data',
            'code'    => $code,
            'details' => $violations,
        ];
    }
}
