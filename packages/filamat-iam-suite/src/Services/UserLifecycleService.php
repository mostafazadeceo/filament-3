<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;

class UserLifecycleService
{
    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService
    ) {}

    public function activate(Tenant $tenant, Authenticatable $user, ?Authenticatable $actor = null, ?string $reason = null): void
    {
        $this->updateMembership($tenant, $user, [
            'status' => 'active',
            'activated_at' => now(),
            'activated_by_id' => $actor?->getAuthIdentifier(),
        ]);

        $this->auditService->log('user.activated', $user, ['reason' => $reason], $actor, $tenant);
        $this->securityEventService->record('user.activated', 'info', $actor, $tenant, [
            'user_id' => $user->getAuthIdentifier(),
        ]);

        $this->syncChatOnActivate($tenant, $user);
    }

    public function suspend(Tenant $tenant, Authenticatable $user, ?Authenticatable $actor = null, ?string $reason = null): void
    {
        $this->updateMembership($tenant, $user, [
            'status' => 'inactive',
            'suspended_at' => now(),
            'suspension_reason' => $reason,
            'suspended_by_id' => $actor?->getAuthIdentifier(),
        ]);

        $this->auditService->log('user.suspended', $user, ['reason' => $reason], $actor, $tenant);
        $this->securityEventService->record('user.suspended', 'warning', $actor, $tenant, [
            'user_id' => $user->getAuthIdentifier(),
        ]);

        $this->syncChatOnSuspend($tenant, $user);
    }

    protected function updateMembership(Tenant $tenant, Authenticatable $user, array $data): void
    {
        if (! method_exists($user, 'tenants')) {
            return;
        }

        $user->tenants()->syncWithoutDetaching([
            $tenant->getKey() => $data,
        ]);
    }

    protected function syncChatOnActivate(Tenant $tenant, Authenticatable $user): void
    {
        if (! config('filament-chat.auto_sync', false)) {
            return;
        }

        if (! class_exists(\Haida\FilamentChat\Models\ChatConnection::class)) {
            return;
        }

        try {
            $connection = \Haida\FilamentChat\Models\ChatConnection::query()
                ->where('tenant_id', $tenant->getKey())
                ->default()
                ->first();

            if (! $connection) {
                return;
            }

            if (class_exists(\Haida\FilamentChat\Services\ChatConnectionService::class)) {
                app(\Haida\FilamentChat\Services\ChatConnectionService::class)->syncUser($connection, $user);
            }
        } catch (\Throwable) {
            // swallow to avoid lifecycle failures
        }
    }

    protected function syncChatOnSuspend(Tenant $tenant, Authenticatable $user): void
    {
        if (! config('filament-chat.auto_deactivate', false)) {
            return;
        }

        if (! class_exists(\Haida\FilamentChat\Models\ChatConnection::class)) {
            return;
        }

        try {
            $connection = \Haida\FilamentChat\Models\ChatConnection::query()
                ->where('tenant_id', $tenant->getKey())
                ->default()
                ->first();

            if (! $connection) {
                return;
            }

            if (class_exists(\Haida\FilamentChat\Services\ChatConnectionService::class)) {
                app(\Haida\FilamentChat\Services\ChatConnectionService::class)->deactivateUser($connection, $user);
            }
        } catch (\Throwable) {
            // swallow to avoid lifecycle failures
        }
    }
}
