<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Automation;

use Filamat\IamSuite\Models\Webhook;
use Illuminate\Support\Facades\RateLimiter;

class AutomationRateLimiter
{
    public function allows(Webhook $webhook, ?int $tenantId): bool
    {
        $defaults = (array) config('filamat-iam.automation.rate_limit', []);
        $overrides = is_array($webhook->rate_limit ?? null) ? $webhook->rate_limit : [];
        $limit = array_merge($defaults, $overrides);

        $max = (int) ($limit['max_attempts'] ?? 60);
        $decay = (int) ($limit['decay_seconds'] ?? 60);

        if ($max <= 0 || $decay <= 0) {
            return true;
        }

        $key = implode(':', [
            'iam-automation',
            (string) ($tenantId ?? 'global'),
            (string) $webhook->getKey(),
        ]);

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return false;
        }

        RateLimiter::hit($key, $decay);

        return true;
    }
}
