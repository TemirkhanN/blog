<?php

declare(strict_types=1);

namespace App\Service;

class TokenIssuer
{
    /**
     * @var array<{
     *   login: string,
     *   password: string
     * }>
     */
    private array $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function createToken(string $login, string $password): ?string
    {
        foreach ($this->users as $user) {
            if ($user['login'] === $login) {
                if ($password === $user['password']) {
                    return password_hash($password, PASSWORD_BCRYPT);
                }
            }
        }

        return null;
    }
}
