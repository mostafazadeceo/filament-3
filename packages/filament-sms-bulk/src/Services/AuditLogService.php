<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services;

use Haida\SmsBulk\Models\SmsBulkAuditLog;

class AuditLogService
{
    /**
     * @param array<string, mixed> $meta
     */
    public function log(
        int $tenantId,
        string $action,
        ?int $actorId = null,
        ?string $actorType = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $meta = [],
        ?string $ip = null,
        ?string $userAgent = null,
    ): SmsBulkAuditLog {
        return SmsBulkAuditLog::create([
            'tenant_id' => $tenantId,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'meta' => $meta,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}
