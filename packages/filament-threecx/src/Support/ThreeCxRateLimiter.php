<?php

namespace Haida\FilamentThreeCx\Support;

use Illuminate\Cache\RateLimiter;

class ThreeCxRateLimiter
{
    public function __construct(
        protected RateLimiter $limiter
    ) {}

    public function throttle(string $key, int $maxRequests, int $perSeconds): void
    {
        $decaySeconds = max(1, $perSeconds);

        while ($this->limiter->tooManyAttempts($key, $maxRequests)) {
            $sleepFor = max(1, $this->limiter->availableIn($key));
            sleep($sleepFor);
        }

        $this->limiter->hit($key, $decaySeconds);
    }
}
