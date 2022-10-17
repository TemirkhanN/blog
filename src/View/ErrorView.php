<?php

declare(strict_types=1);

namespace App\View;

use TemirkhanN\Generic\ErrorInterface;

class ErrorView
{
    /**
     * @param ErrorInterface $error
     *
     * @return array{
     *     message: string,
     *     code: int,
     *     details: array<mixed>
     * }
     */
    public static function create(ErrorInterface $error): array
    {
        return [
            'message' => $error->getMessage(),
            'code'    => $error->getCode(),
            'details' => $error->getDetails(),
        ];
    }
}
