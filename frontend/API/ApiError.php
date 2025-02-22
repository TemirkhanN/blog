<?php

declare(strict_types=1);

namespace Frontend\API;

enum ApiError: int
{
    case TEMPORARILY_UNREACHABLE = 1;
    case RESOURCE_NOT_FOUND      = 2;
}
