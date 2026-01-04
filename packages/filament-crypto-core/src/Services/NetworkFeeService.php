<?php

namespace Haida\FilamentCryptoCore\Services;

use Haida\FilamentCryptoCore\Models\CryptoNetworkFee;
use Illuminate\Support\Facades\Cache;

class NetworkFeeService
{
    public function getFee(string $currency, string $network): ?CryptoNetworkFee
    {
        $ttl = (int) config('filament-crypto-core.defaults.fee_ttl_seconds', 300);
        $key = sprintf('crypto_network_fee:%s:%s', strtolower($currency), strtolower($network));

        return Cache::remember($key, $ttl, function () use ($currency, $network) {
            return CryptoNetworkFee::query()
                ->where('currency', strtoupper($currency))
                ->where('network', strtoupper($network))
                ->orderByDesc('quoted_at')
                ->first();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeFee(string $currency, string $network, string $model, array $data = []): CryptoNetworkFee
    {
        return CryptoNetworkFee::query()->create([
            'currency' => strtoupper($currency),
            'network' => strtoupper($network),
            'fee_model' => $model,
            'data' => $data,
            'quoted_at' => now(),
        ]);
    }
}
