<?php

namespace App\Support\Navigation;

use Illuminate\Support\Str;

class AppContext
{
    public const SESSION_KEY = 'abrak_app';

    public static function get(): ?string
    {
        $value = session()->get(self::SESSION_KEY);

        return is_string($value) && $value !== '' ? $value : null;
    }

    public static function set(?string $key): void
    {
        if (is_string($key) && $key !== '') {
            session()->put(self::SESSION_KEY, $key);
            return;
        }

        session()->forget(self::SESSION_KEY);
    }

    public static function keyFromLabel(string $label): string
    {
        $slug = Str::slug($label, '-');

        if ($slug !== '') {
            return $slug;
        }

        $normalized = Str::of($label)
            ->trim()
            ->replaceMatches('/\s+/', '-')
            ->lower()
            ->toString();

        return $normalized !== '' ? $normalized : 'app-' . substr(md5($label), 0, 8);
    }
}
