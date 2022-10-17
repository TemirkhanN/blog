<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use TemirkhanN\Generic\ErrorInterface;

class InvalidData implements ErrorInterface
{
    /**
     * @param string       $message
     * @param int          $code
     * @param array<mixed> $details
     */
    public function __construct(
        private readonly string $message,
        private readonly int $code,
        private readonly array $details
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public static function fromConstraintsViolation(ConstraintViolationListInterface $violations): self
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return new self('Invalid data', 0, $errors);
    }
}
