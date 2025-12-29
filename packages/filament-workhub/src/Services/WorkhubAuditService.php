<?php

namespace Haida\FilamentWorkhub\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\AuditEvent;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\WorkItem;

class WorkhubAuditService
{
    public function log(string $event, ?Project $project = null, ?WorkItem $workItem = null, array $payload = []): AuditEvent
    {
        $tenantId = $project?->tenant_id ?? $workItem?->tenant_id ?? TenantContext::getTenantId();

        return AuditEvent::query()->create([
            'tenant_id' => $tenantId,
            'project_id' => $project?->getKey() ?? $workItem?->project_id,
            'work_item_id' => $workItem?->getKey(),
            'actor_id' => auth()->id(),
            'event' => $event,
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }
}
