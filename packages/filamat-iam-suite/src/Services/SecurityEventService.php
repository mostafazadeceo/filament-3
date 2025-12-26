<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\SecurityEvent;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class SecurityEventService
{
    public function record(
        string $type,
        string $severity = 'info',
        ?Authenticatable $user = null,
        ?Tenant $tenant = null,
        array $meta = [],
        ?Request $request = null
    ): SecurityEvent {
        $tenant ??= TenantContext::getTenant();
        $request ??= request();

        return SecurityEvent::query()->create([
            'tenant_id' => $tenant?->getKey(),
            'user_id' => $user?->getAuthIdentifier(),
            'type' => $type,
            'severity' => $severity,
            'meta' => $meta,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'occurred_at' => now(),
        ]);
    }
}
