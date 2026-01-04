<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\AuditLog;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\Automation\IamEventFactory;
use Filamat\IamSuite\Services\Automation\IamEventPublisher;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    public function __construct(
        protected AuditHashService $hashService,
        protected IamEventFactory $eventFactory,
        protected IamEventPublisher $publisher,
    ) {}

    public function log(
        string $action,
        ?Model $subject = null,
        array $diff = [],
        ?Authenticatable $actor = null,
        ?Tenant $tenant = null,
        ?Request $request = null
    ): AuditLog {
        $actor ??= auth()->user();
        $tenant ??= $this->resolveTenant($subject) ?? TenantContext::getTenant();
        $request ??= request();

        $createdAt = now();
        $payload = [
            'tenant_id' => $tenant?->getKey(),
            'actor_id' => $actor?->getAuthIdentifier(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'diff' => $diff,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => $createdAt->toISOString(),
        ];

        $hashData = [];
        if ((bool) config('filamat-iam.audit.hash_chain', true)) {
            $hashData = $this->hashService->buildHash($tenant, $payload);
        }

        $record = AuditLog::query()->create(array_merge([
            'tenant_id' => $tenant?->getKey(),
            'actor_id' => $actor?->getAuthIdentifier(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'diff' => $diff,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => $createdAt,
        ], $hashData));

        $automationEvent = $this->eventFactory->fromAudit($action, $subject, $diff, $actor, $tenant);
        if ($automationEvent) {
            $this->publisher->publish($automationEvent);
        }

        return $record;
    }

    protected function resolveTenant(?Model $subject): ?Tenant
    {
        if (! $subject) {
            return null;
        }

        if (method_exists($subject, 'tenant')) {
            return $subject->tenant;
        }

        if (property_exists($subject, 'tenant_id') || $subject->getAttribute('tenant_id')) {
            $tenantId = $subject->getAttribute('tenant_id');
            if ($tenantId) {
                return Tenant::query()->find($tenantId);
            }
        }

        return null;
    }
}
