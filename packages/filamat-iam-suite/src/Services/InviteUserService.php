<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Events\UserInvited;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\UserInvitation;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InviteUserService
{
    public function __construct(
        protected AuditService $auditService,
        protected NotificationService $notificationService,
        protected SecurityEventService $securityEventService,
        protected RoleTemplateService $roleTemplateService,
        protected OrganizationEntitlementService $entitlementService,
        protected ModuleCatalog $moduleCatalog
    ) {}

    /**
     * @param  array<int, string>  $roles
     * @param  array<int, string>  $permissions
     * @return array{invitation: UserInvitation, token: string}
     */
    public function invite(
        Tenant $tenant,
        string $email,
        ?string $name = null,
        array $roles = [],
        array $permissions = [],
        ?Authenticatable $actor = null,
        ?string $reason = null,
        ?\DateTimeInterface $expiresAt = null
    ): array {
        if (! $this->entitlementService->canInviteUser($tenant)) {
            throw new \RuntimeException('سقف کاربران مجاز برای این سازمان تکمیل شده است.');
        }

        $userModel = config('auth.providers.users.model');
        $user = $userModel::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name ?: $email,
                'password' => bcrypt(Str::random(20)),
            ]
        );

        $pivotRole = $this->resolvePivotRole($roles);
        if (method_exists($user, 'tenants')) {
            $user->tenants()->syncWithoutDetaching([
                $tenant->getKey() => [
                    'role' => $pivotRole,
                    'status' => 'invited',
                    'joined_at' => now(),
                    'invited_at' => now(),
                    'invited_by_id' => $actor?->getAuthIdentifier(),
                ],
            ]);
        }

        $token = Str::random(64);
        $invitation = UserInvitation::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'invited_by_id' => $actor?->getAuthIdentifier(),
            'email' => $email,
            'roles' => array_values(array_unique($roles)),
            'permissions' => array_values(array_unique($permissions)),
            'token_hash' => hash('sha256', $token),
            'status' => 'pending',
            'reason' => $reason,
            'expires_at' => $expiresAt,
        ]);

        $this->applyDefaults($tenant, $user, $permissions, $roles);
        if ($roles === []) {
            $modulePermissions = $this->resolveAllowedPermissions($tenant);
            $this->roleTemplateService->syncTemplatesForTenant($tenant, $modulePermissions);
            $this->roleTemplateService->assignRoleForPivot($tenant, $user, $pivotRole);
        }

        event(new UserInvited($user, $tenant));

        $this->notificationService->sendNotification($user, 'user.invited', [
            'message' => 'دعوت‌نامه ارسال شد.',
            'token' => $token,
            'tenant_id' => $tenant->getKey(),
        ], $tenant);

        $this->auditService->log('user.invited', $invitation, [
            'email' => $email,
            'roles' => $roles,
            'permissions' => $permissions,
        ], $actor, $tenant);

        $this->securityEventService->record('user.invited', 'info', $actor, $tenant, [
            'invitation_id' => $invitation->getKey(),
            'user_id' => $user->getAuthIdentifier(),
        ]);

        return ['invitation' => $invitation, 'token' => $token];
    }

    public function accept(UserInvitation $invitation, string $token, ?Authenticatable $actor = null): UserInvitation
    {
        $tenant = $invitation->tenant ?? TenantContext::getTenant();
        if (! $tenant) {
            throw new \RuntimeException('فضای کاری یافت نشد.');
        }

        if ($invitation->status !== 'pending') {
            throw new \RuntimeException('دعوت‌نامه معتبر نیست.');
        }

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->update(['status' => 'expired']);
            throw new \RuntimeException('دعوت‌نامه منقضی شده است.');
        }

        if (! hash_equals($invitation->token_hash, hash('sha256', $token))) {
            throw new \RuntimeException('توکن دعوت معتبر نیست.');
        }

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $user = $invitation->user;
        if ($user && method_exists($user, 'tenants')) {
            $user->tenants()->syncWithoutDetaching([
                $tenant->getKey() => [
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by_id' => $actor?->getAuthIdentifier() ?? $user->getAuthIdentifier(),
                ],
            ]);
        }

        $this->applyRolesAndPermissions($tenant, $user, $invitation->permissions ?? [], $invitation->roles ?? []);

        $this->auditService->log('user.activated', $invitation, [], $actor, $tenant);
        $this->securityEventService->record('user.activated', 'info', $actor ?? $user, $tenant, [
            'invitation_id' => $invitation->getKey(),
        ]);

        $this->syncChatOnActivate($tenant, $user);

        return $invitation;
    }

    public function revoke(UserInvitation $invitation, ?Authenticatable $actor = null, ?string $reason = null): UserInvitation
    {
        $tenant = $invitation->tenant ?? TenantContext::getTenant();

        $invitation->update([
            'status' => 'revoked',
            'reason' => $reason,
        ]);

        $user = $invitation->user;
        if ($tenant && $user && method_exists($user, 'tenants')) {
            $user->tenants()->syncWithoutDetaching([
                $tenant->getKey() => [
                    'status' => 'inactive',
                    'suspended_at' => now(),
                    'suspension_reason' => $reason,
                    'suspended_by_id' => $actor?->getAuthIdentifier(),
                ],
            ]);
        }

        $this->auditService->log('user.invite_revoked', $invitation, ['reason' => $reason], $actor, $tenant);
        $this->securityEventService->record('user.invite_revoked', 'warning', $actor, $tenant, [
            'invitation_id' => $invitation->getKey(),
        ]);

        if ($tenant && $user) {
            $this->syncChatOnSuspend($tenant, $user);
        }

        return $invitation;
    }

    /**
     * @param  array<int, string>  $permissions
     * @param  array<int, string>  $roles
     */
    protected function applyDefaults(Tenant $tenant, Authenticatable $user, array $permissions, array $roles): void
    {
        if ($permissions === []) {
            $permissions = AccessSettings::personDefaultPermissions($tenant);
        }

        if ($roles === []) {
            $roles = AccessSettings::personDefaultRoles($tenant);
        }

        $this->applyRolesAndPermissions($tenant, $user, $permissions, $roles);
    }

    /**
     * @param  array<int, string>  $permissions
     * @param  array<int, string>  $roles
     */
    protected function applyRolesAndPermissions(Tenant $tenant, Authenticatable $user, array $permissions, array $roles): void
    {
        foreach (Arr::wrap($permissions) as $permissionKey) {
            PermissionOverride::query()->updateOrCreate([
                'tenant_id' => $tenant->getKey(),
                'user_id' => $user->getAuthIdentifier(),
                'permission_key' => $permissionKey,
            ], [
                'effect' => 'allow',
            ]);
        }

        if (! method_exists($user, 'assignRole')) {
            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

        foreach (Arr::wrap($roles) as $roleName) {
            $role = Role::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('name', $roleName)
                ->first();

            if ($role) {
                $user->assignRole($role);
            }
        }
    }

    /**
     * @param  array<int, string>  $roles
     */
    protected function resolvePivotRole(array $roles): string
    {
        if (in_array('tenant_owner', $roles, true)) {
            return 'owner';
        }

        if (in_array('tenant_admin', $roles, true)) {
            return 'admin';
        }

        return 'member';
    }

    /**
     * @return array<int, string>
     */
    protected function resolveAllowedPermissions(Tenant $tenant): array
    {
        $modules = $this->entitlementService->allowedModulesForTenant($tenant);

        return $this->moduleCatalog->permissionsForModules($modules);
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
            // swallow to avoid invite failures
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
            // swallow to avoid invite failures
        }
    }
}
