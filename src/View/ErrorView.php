<?php

declare(strict_types=1);

namespace App\View;

use App\Service\Post\Dto\InvalidInput;
use TemirkhanN\Generic\ErrorInterface;

class ErrorView
{
    /**
     * @param ErrorInterface|InvalidInput $error
     *
     * @return array{
     *     message: string,
     *     code: int,
     *     details: array<mixed>
     * }
     */
    public static function create(ErrorInterface|InvalidInput $error): array
    {
        return [
            'message' => $error->getMessage(),
            'code'    => $error->getCode(),
            'details' => $error->getDetails(),
        ];
    }
}
