<?php

namespace Haida\FilamentThreeCx\Services;

use DateTimeInterface;
use Haida\FilamentThreeCx\Contracts\ThreeCxTokenProviderInterface;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Models\ThreeCxTokenCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ThreeCxTokenProvider implements ThreeCxTokenProviderInterface
{
    public function getToken(ThreeCxInstance $instance, string $scope): ?array
    {
        $cacheEnabled = (bool) config('filament-threecx.cache.enabled', true);
        $cache = $this->cacheStore();
        $cacheKey = $this->cacheKey($instance, $scope);

        if ($cacheEnabled) {
            $cached = $cache->get($cacheKey);
            if (is_array($cached) && isset($cached['access_token'], $cached['expires_at'])) {
                $expiresAt = Carbon::parse($cached['expires_at']);
                if ($expiresAt->isFuture()) {
                    return [
                        'access_token' => (string) $cached['access_token'],
                        'expires_at' => $expiresAt,
                    ];
                }
            }
        }

        if (! (bool) config('filament-threecx.cache.db_fallback', true)) {
            return null;
        }

        $record = ThreeCxTokenCache::query()
            ->where('tenant_id', $instance->tenant_id)
            ->where('instance_id', $instance->getKey())
            ->where('scope', $scope)
            ->first();

        if (! $record || ! $record->expires_at || ! $record->expires_at->isFuture()) {
            return null;
        }

        $token = [
            'access_token' => (string) $record->access_token,
            'expires_at' => $record->expires_at,
        ];

        if ($cacheEnabled) {
            $this->storeCache($cacheKey, $token['access_token'], $token['expires_at']);
        }

        return $token;
    }

    public function storeToken(ThreeCxInstance $instance, string $scope, string $token, DateTimeInterface $expiresAt): void
    {
        $cacheEnabled = (bool) config('filament-threecx.cache.enabled', true);
        if ($cacheEnabled) {
            $this->storeCache($this->cacheKey($instance, $scope), $token, $expiresAt);
        }

        if ((bool) config('filament-threecx.cache.db_fallback', true)) {
            ThreeCxTokenCache::query()->updateOrCreate([
                'tenant_id' => $instance->tenant_id,
                'instance_id' => $instance->getKey(),
                'scope' => $scope,
            ], [
                'access_token' => $token,
                'expires_at' => $expiresAt,
            ]);
        }
    }

    public function forgetToken(ThreeCxInstance $instance, string $scope): void
    {
        $cacheEnabled = (bool) config('filament-threecx.cache.enabled', true);
        if ($cacheEnabled) {
            $this->cacheStore()->forget($this->cacheKey($instance, $scope));
        }

        if ((bool) config('filament-threecx.cache.db_fallback', true)) {
            ThreeCxTokenCache::query()
                ->where('tenant_id', $instance->tenant_id)
                ->where('instance_id', $instance->getKey())
                ->where('scope', $scope)
                ->delete();
        }
    }

    protected function cacheStore()
    {
        $store = config('filament-threecx.cache.store');

        return $store ? Cache::store($store) : Cache::store();
    }

    protected function cacheKey(ThreeCxInstance $instance, string $scope): string
    {
        return 'threecx:token:'.$instance->tenant_id.':'.$instance->getKey().':'.$scope;
    }

    protected function storeCache(string $key, string $token, DateTimeInterface $expiresAt): void
    {
        $ttl = max(1, Carbon::now()->diffInSeconds(Carbon::instance($expiresAt), false));
        $payload = [
            'access_token' => $token,
            'expires_at' => Carbon::instance($expiresAt)->toIso8601String(),
        ];

        $this->cacheStore()->put($key, $payload, $ttl);
    }
}
