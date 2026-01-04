<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Services;

use Haida\FilamentCryptoCore\Models\CryptoAuditEvent;

class AuditLogService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function record(int $tenantId, string $action, ?int $actorUserId = null, ?string $targetType = null, ?string $targetId = null, ?string $reason = null, array $payload = []): CryptoAuditEvent
    {
        return CryptoAuditEvent::query()->create([
            'tenant_id' => $tenantId,
            'event_type' => $action,
            'subject_type' => $targetType,
            'subject_id' => $targetId,
            'description' => $reason,
            'meta' => $payload,
            'created_at' => now(),
        ]);
    }
}
