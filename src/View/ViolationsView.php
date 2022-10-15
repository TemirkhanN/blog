<?php

declare(strict_types=1);

namespace App\View;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Validation errors view
 */
class ViolationsView
{
    /**
     * Creates view
     *
     * @param ConstraintViolationListInterface $violationList
     *
     * @return array<string, string>
     */
    public static function create(ConstraintViolationListInterface $violationList): array
    {
        $view = [];
        foreach ($violationList as $violation) {
            $view[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return $view;
    }
}
