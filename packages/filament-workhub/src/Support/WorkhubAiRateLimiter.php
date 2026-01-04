<?php

namespace Haida\FilamentWorkhub\Support;

use Illuminate\Cache\RateLimiter;

class WorkhubAiRateLimiter
{
    public function __construct(protected RateLimiter $limiter) {}

    public function throttle(string $key, int $maxPerMinute): void
    {
        $decaySeconds = 60;

        while ($this->limiter->tooManyAttempts($key, $maxPerMinute)) {
            $sleepFor = max(1, $this->limiter->availableIn($key));
            usleep($sleepFor * 1000000);
        }

        $this->limiter->hit($key, $decaySeconds);
    }
}
