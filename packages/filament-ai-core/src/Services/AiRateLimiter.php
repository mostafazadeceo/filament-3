<?php

namespace Haida\FilamentAiCore\Services;

use Illuminate\Cache\RateLimiter;

class AiRateLimiter
{
    public function __construct(protected RateLimiter $limiter) {}

    public function throttle(?int $tenantId, string $module, string $actionType): void
    {
        if (! (bool) config('filament-ai-core.rate_limit.enabled', true)) {
            return;
        }

        $maxPerMinute = $this->resolveLimit($module, $actionType);
        if ($maxPerMinute <= 0) {
            return;
        }

        $key = sprintf('ai-core:%s:%s:%s', $tenantId ?: 'global', $module, $actionType);
        $decaySeconds = 60;

        while ($this->limiter->tooManyAttempts($key, $maxPerMinute)) {
            $sleepFor = max(1, $this->limiter->availableIn($key));
            usleep($sleepFor * 1000000);
        }

        $this->limiter->hit($key, $decaySeconds);
    }

    protected function resolveLimit(string $module, string $actionType): int
    {
        $overrides = (array) config('filament-ai-core.rate_limit.overrides', []);
        $override = $overrides[$module.'.'.$actionType] ?? null;
        if ($override !== null) {
            return max(0, (int) $override);
        }

        return max(0, (int) config('filament-ai-core.rate_limit.max_per_minute', 60));
    }
}
