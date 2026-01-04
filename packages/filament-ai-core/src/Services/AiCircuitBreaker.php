<?php

namespace Haida\FilamentAiCore\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AuditService;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Illuminate\Contracts\Cache\Repository;

class AiCircuitBreaker
{
    public function __construct(
        protected Repository $cache,
        protected AuditService $auditService,
    ) {}

    public function isDisabled(?int $tenantId, string $provider): bool
    {
        if (! $this->isEnabled() || ! $tenantId || $provider === 'mock') {
            return false;
        }

        return (bool) $this->cache->get($this->disabledKey($tenantId, $provider));
    }

    public function recordFailure(?int $tenantId, string $provider, ?string $error = null): void
    {
        if (! $this->isEnabled() || ! $tenantId || $provider === 'mock') {
            return;
        }

        if ($this->isDisabled($tenantId, $provider)) {
            return;
        }

        $countKey = $this->countKey($tenantId, $provider);
        $window = $this->windowSeconds();
        $threshold = $this->failureThreshold();

        $count = (int) $this->cache->get($countKey, 0) + 1;
        $this->cache->put($countKey, $count, $window);

        if ($count >= $threshold) {
            $this->trip($tenantId, $provider, $error);
        }
    }

    public function recordSuccess(?int $tenantId, string $provider): void
    {
        if (! $this->isEnabled() || ! $tenantId || $provider === 'mock') {
            return;
        }

        $this->cache->forget($this->countKey($tenantId, $provider));
    }

    protected function trip(int $tenantId, string $provider, ?string $error = null): void
    {
        $cooldown = $this->cooldownSeconds();
        $disabledUntil = now()->addSeconds($cooldown)->toIso8601String();

        $this->cache->put($this->disabledKey($tenantId, $provider), $disabledUntil, $cooldown);
        $this->cache->forget($this->countKey($tenantId, $provider));

        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant) {
            return;
        }

        $this->auditService->log('ai.provider.disabled', $tenant, [
            'provider' => $provider,
            'disabled_until' => $disabledUntil,
            'error' => $error,
        ], null, $tenant);

        if (! $this->shouldNotify()) {
            return;
        }

        if (class_exists(TriggerDispatcher::class)) {
            $panelId = (string) config('filament-ai-core.notifications.panel', 'tenant');
            app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $tenant, 'ai.provider.disabled');
        }
    }

    protected function isEnabled(): bool
    {
        return (bool) config('filament-ai-core.circuit_breaker.enabled', true);
    }

    protected function failureThreshold(): int
    {
        return max(1, (int) config('filament-ai-core.circuit_breaker.failure_threshold', 3));
    }

    protected function windowSeconds(): int
    {
        return max(60, (int) config('filament-ai-core.circuit_breaker.window_seconds', 300));
    }

    protected function cooldownSeconds(): int
    {
        return max(60, (int) config('filament-ai-core.circuit_breaker.cooldown_seconds', 600));
    }

    protected function shouldNotify(): bool
    {
        return (bool) config('filament-ai-core.circuit_breaker.notify', true);
    }

    protected function countKey(int $tenantId, string $provider): string
    {
        return sprintf('ai-core:breaker:%d:%s:count', $tenantId, $provider);
    }

    protected function disabledKey(int $tenantId, string $provider): string
    {
        return sprintf('ai-core:breaker:%d:%s:disabled', $tenantId, $provider);
    }
}
