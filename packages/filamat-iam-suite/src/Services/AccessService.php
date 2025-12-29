<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Group;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\AccessSettings;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessService
{
    public function checkPermission(Authenticatable $user, Tenant $tenant, string $permissionKey): bool
    {
        $result = $this->explainPermission($user, $tenant, $permissionKey);

        return $result['allowed'];
    }

    /**
     * @return array{allowed: bool, trace: array<int, array{source: string, effect: string, detail: string}>}
     */
    public function explainPermission(Authenticatable $user, Tenant $tenant, string $permissionKey): array
    {
        $trace = [];

        if (! $this->passesTenantAccessPolicy($tenant, $permissionKey, $trace)) {
            return [
                'allowed' => false,
                'trace' => $trace,
            ];
        }

        $override = PermissionOverride::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('user_id', $user->getAuthIdentifier())
            ->where('permission_key', $permissionKey)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('id')
            ->first();

        if ($override) {
            $trace[] = [
                'source' => 'user_override',
                'effect' => $override->effect,
                'detail' => 'بازنویسی کاربر',
            ];

            if ($override->effect === 'allow' && ! $this->passesSubscriptionGate($user, $tenant, $permissionKey, $trace)) {
                return [
                    'allowed' => false,
                    'trace' => $trace,
                ];
            }

            return [
                'allowed' => $override->effect === 'allow',
                'trace' => $trace,
            ];
        }

        $groupResult = $this->evaluateGroupPermission($user, $tenant, $permissionKey);
        $trace = array_merge($trace, $groupResult['trace']);
        if ($groupResult['decided']) {
            if ($groupResult['allowed'] && ! $this->passesSubscriptionGate($user, $tenant, $permissionKey, $trace)) {
                return [
                    'allowed' => false,
                    'trace' => $trace,
                ];
            }

            return [
                'allowed' => $groupResult['allowed'],
                'trace' => $trace,
            ];
        }

        $rolePermission = $this->roleHasPermission($user, $tenant, $permissionKey);
        if ($rolePermission) {
            $trace[] = [
                'source' => 'role',
                'effect' => 'allow',
                'detail' => 'نقش کاربر',
            ];

            if (! $this->passesSubscriptionGate($user, $tenant, $permissionKey, $trace)) {
                return [
                    'allowed' => false,
                    'trace' => $trace,
                ];
            }

            return [
                'allowed' => true,
                'trace' => $trace,
            ];
        }

        $trace[] = [
            'source' => 'default',
            'effect' => 'deny',
            'detail' => 'بدون مجوز',
        ];

        return [
            'allowed' => false,
            'trace' => $trace,
        ];
    }

    protected function passesSubscriptionGate(Authenticatable $user, Tenant $tenant, string $permissionKey, array &$trace): bool
    {
        if (! (bool) config('filamat-iam.subscriptions.enforce_access', true)) {
            return true;
        }

        $exempt = (array) config('filamat-iam.subscriptions.exempt_permissions', []);
        if (in_array($permissionKey, $exempt, true)) {
            $trace[] = [
                'source' => 'subscription',
                'effect' => 'allow',
                'detail' => 'مجوز از بررسی اشتراک معاف است.',
            ];

            return true;
        }

        $subscription = $this->resolveActiveSubscription($user, $tenant);
        if (! $subscription || ! $subscription->plan) {
            $trace[] = [
                'source' => 'subscription',
                'effect' => 'deny',
                'detail' => 'اشتراک فعال یافت نشد.',
            ];

            return false;
        }

        if (! $this->planAllowsPermission($subscription->plan, $permissionKey)) {
            $trace[] = [
                'source' => 'subscription',
                'effect' => 'deny',
                'detail' => 'پلن اجازه این مجوز را ندارد.',
            ];

            return false;
        }

        $trace[] = [
            'source' => 'subscription',
            'effect' => 'allow',
            'detail' => 'اشتراک فعال است.',
        ];

        return true;
    }

    protected function resolveActiveSubscription(Authenticatable $user, Tenant $tenant): ?Subscription
    {
        $statuses = (array) config('filamat-iam.subscriptions.active_statuses', ['active', 'trialing']);

        return Subscription::query()
            ->where('tenant_id', $tenant->getKey())
            ->whereIn('status', $statuses)
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->getAuthIdentifier());
            })
            ->orderByRaw('user_id is not null desc')
            ->with('plan')
            ->first();
    }

    protected function planAllowsPermission(SubscriptionPlan $plan, string $permissionKey): bool
    {
        $features = $plan->features ?? [];
        if (! is_array($features)) {
            return true;
        }

        if (isset($features['permissions']) && is_array($features['permissions']) && $features['permissions'] !== []) {
            return in_array($permissionKey, $features['permissions'], true);
        }

        return true;
    }

    protected function evaluateGroupPermission(Authenticatable $user, Tenant $tenant, string $permissionKey): array
    {
        $trace = [];

        $allowedRoles = [];
        if (AccessSettings::companyEnforced($tenant)) {
            $allowedRoles = AccessSettings::companyAllowedRoles($tenant);
        }

        $groups = Group::query()
            ->where('tenant_id', $tenant->getKey())
            ->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->getAuthIdentifier());
            })
            ->with(['permissions', 'roles.permissions'])
            ->get();

        $groupDenied = false;
        $groupAllowed = false;

        foreach ($groups as $group) {
            foreach ($group->permissions as $permission) {
                if ($permission->name !== $permissionKey) {
                    continue;
                }

                $effect = $permission->pivot?->effect ?? 'allow';
                $trace[] = [
                    'source' => 'group_permission',
                    'effect' => $effect,
                    'detail' => 'گروه: '.$group->name,
                ];

                if ($effect === 'deny') {
                    $groupDenied = true;
                } else {
                    $groupAllowed = true;
                }
            }

            foreach ($group->roles as $role) {
                if ($allowedRoles !== [] && ! in_array($role->name, $allowedRoles, true)) {
                    continue;
                }

                if ($role->permissions->pluck('name')->contains($permissionKey)) {
                    $trace[] = [
                        'source' => 'group_role',
                        'effect' => 'allow',
                        'detail' => 'گروه: '.$group->name.' / نقش: '.$role->name,
                    ];
                    $groupAllowed = true;
                }
            }
        }

        if ($groupDenied) {
            return ['decided' => true, 'allowed' => false, 'trace' => $trace];
        }

        if ($groupAllowed) {
            return ['decided' => true, 'allowed' => true, 'trace' => $trace];
        }

        return ['decided' => false, 'allowed' => false, 'trace' => $trace];
    }

    protected function roleHasPermission(Authenticatable $user, Tenant $tenant, string $permissionKey): bool
    {
        if (method_exists($user, 'hasPermissionTo')) {
            try {
                app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

                return $user->hasPermissionTo($permissionKey);
            } catch (\Throwable) {
                // Fallback to manual checks.
            }
        }

        $permission = Permission::query()
            ->where('name', $permissionKey)
            ->where(function ($query) use ($tenant) {
                $query->whereNull('tenant_id')->orWhere('tenant_id', $tenant->getKey());
            })
            ->first();
        if (! $permission) {
            return false;
        }

        $roles = Role::query()
            ->whereHas('users', function ($query) use ($user, $tenant) {
                $query->where('model_id', $user->getAuthIdentifier())
                    ->where('tenant_id', $tenant->getKey());
            })
            ->with('permissions')
            ->get();

        $allowedRoles = [];
        if (AccessSettings::companyEnforced($tenant)) {
            $allowedRoles = AccessSettings::companyAllowedRoles($tenant);
        }

        foreach ($roles as $role) {
            if ($allowedRoles !== [] && ! in_array($role->name, $allowedRoles, true)) {
                continue;
            }

            if ($role->permissions->pluck('name')->contains($permissionKey)) {
                return true;
            }
        }

        return false;
    }

    protected function passesTenantAccessPolicy(Tenant $tenant, string $permissionKey, array &$trace): bool
    {
        if (! AccessSettings::companyEnforced($tenant)) {
            return true;
        }

        $allowed = AccessSettings::companyAllowedPermissions($tenant);
        if ($allowed === []) {
            return true;
        }

        if (! in_array($permissionKey, $allowed, true)) {
            $trace[] = [
                'source' => 'tenant_policy',
                'effect' => 'deny',
                'detail' => 'مجوز در تنظیمات شرکت غیرفعال است.',
            ];

            return false;
        }

        $trace[] = [
            'source' => 'tenant_policy',
            'effect' => 'allow',
            'detail' => 'مجوز در تنظیمات شرکت مجاز است.',
        ];

        return true;
    }
}
