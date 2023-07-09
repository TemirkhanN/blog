<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;

class Registry
{
    public static function init(ManagerRegistry $doctrine): void
    {
        PostRepository::init($doctrine);
        CommentRepository::init($doctrine);
    }
}
