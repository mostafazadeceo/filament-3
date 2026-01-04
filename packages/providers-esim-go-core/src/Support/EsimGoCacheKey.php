<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Support;

use Illuminate\Support\Str;

class EsimGoCacheKey
{
    public static function make(string $prefix, array $parts = []): string
    {
        $encoded = json_encode($parts);
        $hash = $encoded ? sha1($encoded) : 'no-params';

        return Str::of('esim-go')
            ->append(':')
            ->append($prefix)
            ->append(':')
            ->append($hash)
            ->toString();
    }
}
