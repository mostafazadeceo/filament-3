<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services;

use Haida\SmsBulk\Models\SmsBulkConsentRegistry;
use Haida\SmsBulk\Models\SmsBulkSuppressionList;
use Illuminate\Support\Carbon;

class SuppressionService
{
    public function __construct(protected AuditLogService $auditLog) {}

    /**
     * @param array<int, string> $recipients
     * @return array{allowed: array<int, string>, blocked: array<int, string>}
     */
    public function filterRecipients(int $tenantId, array $recipients, bool $overrideSuppression = false): array
    {
        if ($overrideSuppression) {
            return ['allowed' => array_values(array_unique($recipients)), 'blocked' => []];
        }

        $recipientSet = array_values(array_unique($recipients));
        if ($recipientSet === []) {
            return ['allowed' => [], 'blocked' => []];
        }

        $suppressed = SmsBulkSuppressionList::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('msisdn', $recipientSet)
            ->pluck('msisdn')
            ->all();

        $optedOut = SmsBulkConsentRegistry::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'opt_out')
            ->whereIn('msisdn', $recipientSet)
            ->pluck('msisdn')
            ->all();

        $blocked = array_values(array_unique(array_merge($suppressed, $optedOut)));
        $allowed = array_values(array_diff($recipientSet, $blocked));

        return [
            'allowed' => $allowed,
            'blocked' => $blocked,
        ];
    }

    public function applyOptOut(int $tenantId, string $msisdn, string $source = 'keyword', ?int $actorId = null): void
    {
        SmsBulkSuppressionList::query()->updateOrCreate(
            ['tenant_id' => $tenantId, 'msisdn' => $msisdn],
            ['source' => $source, 'reason' => 'opt-out', 'created_by' => $actorId],
        );

        SmsBulkConsentRegistry::query()->updateOrCreate(
            ['tenant_id' => $tenantId, 'msisdn' => $msisdn],
            ['status' => 'opt_out', 'source' => $source, 'revoked_at' => Carbon::now()],
        );

        $this->auditLog->log(
            tenantId: $tenantId,
            action: 'optout.applied',
            actorId: $actorId,
            actorType: $actorId ? 'user' : null,
            subjectType: SmsBulkSuppressionList::class,
            meta: ['msisdn' => $msisdn, 'source' => $source],
        );
    }

    public function applyOptIn(int $tenantId, string $msisdn, string $source = 'manual', ?int $actorId = null): void
    {
        SmsBulkSuppressionList::query()
            ->where('tenant_id', $tenantId)
            ->where('msisdn', $msisdn)
            ->delete();

        SmsBulkConsentRegistry::query()->updateOrCreate(
            ['tenant_id' => $tenantId, 'msisdn' => $msisdn],
            ['status' => 'opt_in', 'source' => $source, 'consented_at' => Carbon::now(), 'revoked_at' => null],
        );

        $this->auditLog->log(
            tenantId: $tenantId,
            action: 'optin.applied',
            actorId: $actorId,
            actorType: $actorId ? 'user' : null,
            subjectType: SmsBulkConsentRegistry::class,
            meta: ['msisdn' => $msisdn, 'source' => $source],
        );
    }
}
