<?php
declare(strict_types=1);

namespace App\Entity;

class Author
{
    private $userId;

    private $name;

    public function __construct(int $userId, string $name)
    {
        $this->userId = $userId;
        $this->name   = $name;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}