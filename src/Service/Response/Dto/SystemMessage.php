<?php

declare(strict_types=1);

namespace App\Service\Response\Dto;

class SystemMessage implements \JsonSerializable
{
    public const CODE_UNSPECIFIED = 0;

    public function __construct(
        private readonly string $message,
        private readonly int $code = self::CODE_UNSPECIFIED
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

    /**
     * @return array{message:string, code:int}
     */
    public function toArray(): array
    {
        return [
            'code'    => $this->code,
            'message' => $this->message,
        ];
    }

    /**
     * @return array{message:string, code:int}
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
