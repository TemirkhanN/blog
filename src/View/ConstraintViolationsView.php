<?php

declare(strict_types=1);

namespace App\View;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Temirkhan\View\ViewInterface;

/**
 * Validation errors view
 */
class ConstraintViolationsView implements ViewInterface
{
    /**
     * Creates view
     *
     * @param mixed $context
     *
     * @return null|array<string, string>
     */
    public function getView(mixed $context)
    {
        if (!$context instanceof ConstraintViolationListInterface) {
            return null;
        }

        $view = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($context as $violation) {
            $view[(string) $violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return $view;
    }
}
