<?php

declare(strict_types=1);

namespace Frontend\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class Access
{
    private const AUTHENTICATION_COOKIE_NAME = '_authToken';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function isAdmin(): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return false;
        }

        return $request->cookies->has(self::AUTHENTICATION_COOKIE_NAME);
    }
}
