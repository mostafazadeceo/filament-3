<?php

namespace Haida\FilamentCryptoCore\Services;

use Haida\FilamentCryptoCore\Models\CryptoRate;
use Illuminate\Support\Facades\Cache;

class RateService
{
    public function getRate(string $from, string $to): ?CryptoRate
    {
        $ttl = (int) config('filament-crypto-core.defaults.rate_ttl_seconds', 300);
        $key = sprintf('crypto_rate:%s:%s', strtolower($from), strtolower($to));

        return Cache::remember($key, $ttl, function () use ($from, $to) {
            return CryptoRate::query()
                ->where('from', strtoupper($from))
                ->where('to', strtoupper($to))
                ->orderByDesc('quoted_at')
                ->first();
        });
    }

    public function storeRate(string $from, string $to, float $rate, string $source = 'manual'): CryptoRate
    {
        return CryptoRate::query()->create([
            'from' => strtoupper($from),
            'to' => strtoupper($to),
            'rate' => $rate,
            'source' => $source,
            'quoted_at' => now(),
        ]);
    }
}
