<?php

declare(strict_types=1);

namespace App\Helper;

class HashHelper
{
    public static function hash(string $data): string
    {
        return hash('sha256', $data);
    }

    public static function verify(string $data, string $hash): bool
    {
        return hash_equals($hash, self::hash($data));
    }
}