<?php

namespace Haida\FilamentCurrencyRates\Support;

use Illuminate\Support\Facades\Crypt;
use Spatie\LaravelSettings\SettingsCasts\SettingsCast;

class EncryptedCast implements SettingsCast
{
    public function get($payload): ?string
    {
        if (blank($payload)) {
            return null;
        }

        try {
            return Crypt::decryptString($payload);
        } catch (\Throwable) {
            return null;
        }
    }

    public function set($payload): ?string
    {
        if (blank($payload)) {
            return null;
        }

        return Crypt::encryptString((string) $payload);
    }
}
