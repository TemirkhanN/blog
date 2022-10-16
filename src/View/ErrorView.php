<?php

declare(strict_types=1);

namespace App\View;

use TemirkhanN\Generic\Error;

class ErrorView
{
    /**
     * @param Error $error
     *
     * @return array{
     *     message: string,
     *     code: int,
     *     details: array<mixed>
     * }
     */
    public static function create(Error $error): array
    {
        return [
            'message' => $error->getMessage(),
            'code'    => $error->getCode(),
            'details' => $error->getDetails(),
        ];
    }
}
