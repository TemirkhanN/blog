<?php

declare(strict_types=1);

namespace App\Entity\Exception;

use DomainException;

class ImpossibleTransitionException extends DomainException
{
    public static function create(string $appliedTransition, string $currentTransition): self
    {
        return new self(
            sprintf('Transition from %s to %s is impossible', $currentTransition, $appliedTransition)
        );
    }
}
