<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;

interface AuthorRepositoryInterface
{
    public function findByName(string $name): ?Author;
}