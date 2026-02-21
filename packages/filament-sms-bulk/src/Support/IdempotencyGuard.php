<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class IdempotencyGuard
{
    /**
     * @template T
     * @param Closure():T $callback
     * @return T|null
     */
    public function once(string $key, int $seconds, Closure $callback): mixed
    {
        $marker = 'sms-bulk:idempotency:'.$key;
        if (! Cache::add($marker, 1, $seconds)) {
            return null;
        }

        return $callback();
    }
}
