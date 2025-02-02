<?php

declare(strict_types=1);

namespace App\Lib\Response\Cache;

use InvalidArgumentException;

class TTL
{
    private function __construct(private readonly int $seconds)
    {
    }

    public static function seconds(int $seconds): self
    {
        if ($seconds < 0) {
            throw new InvalidArgumentException('TTL can not be negative');
        }

        return new self($seconds);
    }

    public static function minutes(int $minutes): self
    {
        if ($minutes < 0) {
            throw new InvalidArgumentException('TTL can not be negative');
        }

        return self::seconds($minutes * 60);
    }

    public static function hours(int $hours): self
    {
        if ($hours < 0) {
            throw new InvalidArgumentException('TTL can not be negative');
        }

        return self::minutes($hours * 60);
    }

    public static function ttl(int $hours, int $minutes, int $seconds): self
    {
        if ($hours < 0 || $minutes < 0 || $seconds < 0) {
            throw new InvalidArgumentException('TTL can not be negative');
        }

        return self::seconds(($hours * 60 * 60) + ($minutes * 60) + $seconds);
    }

    public function toSeconds(): int
    {
        return $this->seconds;
    }
}
