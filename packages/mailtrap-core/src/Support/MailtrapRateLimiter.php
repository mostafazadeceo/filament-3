<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Support;

use Illuminate\Cache\RateLimiter;

class MailtrapRateLimiter
{
    public function __construct(
        protected RateLimiter $limiter,
    ) {}

    public function throttle(string $key, int $maxPerSecond): void
    {
        $decaySeconds = 1;

        while ($this->limiter->tooManyAttempts($key, $maxPerSecond)) {
            $sleepFor = max(1, $this->limiter->availableIn($key));
            usleep($sleepFor * 1000000);
        }

        $this->limiter->hit($key, $decaySeconds);
    }
}
