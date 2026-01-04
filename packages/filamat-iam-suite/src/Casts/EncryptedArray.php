<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class EncryptedArray implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $decoded = null;

        try {
            $decoded = json_decode(Crypt::decryptString($value), true);
        } catch (Throwable) {
            $decoded = json_decode($value, true);
        }

        return is_array($decoded) ? $decoded : null;
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            return Crypt::encryptString($value);
        }

        return Crypt::encryptString(json_encode($value, JSON_UNESCAPED_UNICODE));
    }
}
