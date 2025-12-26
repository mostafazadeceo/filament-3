<?php

namespace Haida\FilamentRelograde\Support;

use Illuminate\Support\Str;

class RelogradeCacheKey
{
    public static function make(string $prefix, array $parts = []): string
    {
        $encoded = json_encode($parts);
        $hash = $encoded ? sha1($encoded) : 'no-params';

        return Str::of('relograde')
            ->append(':')
            ->append($prefix)
            ->append(':')
            ->append($hash)
            ->toString();
    }
}
