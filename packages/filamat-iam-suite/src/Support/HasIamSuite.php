<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\Group;
use Filamat\IamSuite\Models\Notification;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\Tenant;
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
            ->withPivot(['role', 'status', 'joined_at', 'last_login_at', 'last_logout_at', 'login_attempts', 'security_flags'])
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

    public function createTenantToken(Tenant $tenant, string $name, array $abilities = ['*']): NewAccessToken
    {
        $abilities[] = 'tenant:'.$tenant->getKey();

        return $this->createToken($name, array_values(array_unique($abilities)));
    }
}
