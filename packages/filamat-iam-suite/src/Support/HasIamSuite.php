<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\Group;
use Filamat\IamSuite\Models\ImpersonationSession;
use Filamat\IamSuite\Models\MfaMethod;
use Filamat\IamSuite\Models\Notification;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\PrivilegeActivation;
use Filamat\IamSuite\Models\PrivilegeEligibility;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\UserInvitation;
use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\NewAccessToken;

trait HasIamSuite
{
    public function hasIamSuiteSuperAdmin(): bool
    {
        return (bool) ($this->is_super_admin ?? false);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')
            ->withPivot([
                'role',
                'status',
                'joined_at',
                'invited_at',
                'activated_at',
                'suspended_at',
                'suspension_reason',
                'invited_by_id',
                'activated_by_id',
                'suspended_by_id',
                'last_login_at',
                'last_logout_at',
                'login_attempts',
                'security_flags',
            ])
            ->withTimestamps();
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'user_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }

    public function permissionOverrides(): HasMany
    {
        return $this->hasMany(PermissionOverride::class, 'user_id');
    }

    public function iamNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class, 'user_id');
    }

    public function iamInvitations(): HasMany
    {
        return $this->hasMany(UserInvitation::class, 'user_id');
    }

    public function privilegeEligibilities(): HasMany
    {
        return $this->hasMany(PrivilegeEligibility::class, 'user_id');
    }

    public function privilegeActivations(): HasMany
    {
        return $this->hasMany(PrivilegeActivation::class, 'user_id');
    }

    public function iamSessions(): HasMany
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    public function mfaMethods(): HasMany
    {
        return $this->hasMany(MfaMethod::class, 'user_id');
    }

    public function impersonationSessions(): HasMany
    {
        return $this->hasMany(ImpersonationSession::class, 'impersonator_id');
    }

    public function createTenantToken(Tenant $tenant, string $name, array $abilities = ['*']): NewAccessToken
    {
        $abilities[] = 'tenant:'.$tenant->getKey();

        return $this->createToken($name, array_values(array_unique($abilities)));
    }
}
