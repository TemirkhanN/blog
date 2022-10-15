<?php

declare(strict_types=1);

namespace App\Service;

class TokenIssuer
{
    /**
     * @param array<array{login: string, password: string}> $users
     */
    public function __construct(private readonly array $users)
    {
    }

    public function createToken(string $login, string $password): ?string
    {
        foreach ($this->users as $user) {
            if ($user['login'] === $login) {
                if ($password === $user['password']) {
                    return password_hash($password, PASSWORD_BCRYPT) ?: null;
                }
            }
        }

        return null;
    }
}
