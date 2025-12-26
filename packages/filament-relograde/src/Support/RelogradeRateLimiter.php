<?php

namespace Haida\FilamentRelograde\Support;

use Illuminate\Cache\RateLimiter;

class RelogradeRateLimiter
{
    public function __construct(
        protected RateLimiter $limiter
    ) {}

    public function throttle(string $key, int $maxPerMinute): void
    {
        $decaySeconds = 60;

        while ($this->limiter->tooManyAttempts($key, $maxPerMinute)) {
            $sleepFor = max(1, $this->limiter->availableIn($key));
            sleep($sleepFor);
        }

        $this->limiter->hit($key, $decaySeconds);
    }
}
