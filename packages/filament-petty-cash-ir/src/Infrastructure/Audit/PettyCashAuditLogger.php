<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Audit;

use Haida\FilamentPettyCashIr\Models\PettyCashAuditEvent;

class PettyCashAuditLogger implements AuditLoggerInterface
{
    public function log(object $subject, ?int $actorId, string $eventType, ?string $description = null, array $metadata = []): void
    {
        $companyId = (int) ($subject->company_id ?? 0);
        if (! $companyId) {
            return;
        }

        PettyCashAuditEvent::query()->create([
            'tenant_id' => $subject->tenant_id ?? null,
            'company_id' => $companyId,
            'fund_id' => $subject->fund_id ?? null,
            'actor_id' => $actorId,
            'event_type' => $eventType,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'description' => $description,
            'metadata' => array_filter([
                'status' => $subject->status ?? null,
                'source' => 'petty_cash',
                ...$metadata,
            ], fn ($value) => $value !== null),
        ]);
    }
}
