<?php

namespace Haida\FilamentCryptoCore\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Models\CryptoAuditEvent;

class CryptoAuditService
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function record(string $eventType, ?string $subjectType = null, ?string $subjectId = null, ?string $description = null, array $meta = [], ?int $tenantId = null): CryptoAuditEvent
    {
        $tenantId ??= TenantContext::getTenantId();

        return CryptoAuditEvent::query()->create([
            'tenant_id' => $tenantId,
            'event_type' => $eventType,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
