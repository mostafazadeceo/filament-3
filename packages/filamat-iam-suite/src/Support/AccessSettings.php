<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class AccessSettings
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function permissionOptions(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();
        $tenantId = $tenant?->getKey();

        $permissions = Permission::query()
            ->when($tenantId, function ($query) use ($tenantId) {
                $query->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->when(! $tenantId, function ($query) {
                $query->whereNull('tenant_id');
            })
            ->pluck('name')
            ->all();

        $registry = app(CapabilityRegistryInterface::class);
        foreach ($registry->all() as $capability) {
            $permissions = array_merge($permissions, $capability->permissions);
        }

        $permissions = array_values(array_unique(array_filter($permissions)));
        sort($permissions);

        return self::groupPermissions($permissions);
    }

    /**
     * @return array<int, string>
     */
    public static function roleOptions(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();
        $tenantId = $tenant?->getKey();

        $roles = Role::query()
            ->when($tenantId, function ($query) use ($tenantId) {
                $query->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->when(! $tenantId, function ($query) {
                $query->whereNull('tenant_id');
            })
            ->pluck('name')
            ->all();

        $roles = array_values(array_unique(array_filter($roles)));
        sort($roles);

        return $roles;
    }

    /**
     * @return array<int, string>
     */
    public static function companyAllowedPermissions(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();

        return self::normalizeList(data_get($tenant?->settings, 'access.company.allowed_permissions', []));
    }

    /**
     * @return array<int, string>
     */
    public static function companyAllowedRoles(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();

        return self::normalizeList(data_get($tenant?->settings, 'access.company.allowed_roles', []));
    }

    /**
     * @return array<int, string>
     */
    public static function personAllowedPermissions(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();

        return self::normalizeList(data_get($tenant?->settings, 'access.person.allowed_permissions', []));
    }

    /**
     * @return array<int, string>
     */
    public static function personDefaultPermissions(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();

        return self::normalizeList(data_get($tenant?->settings, 'access.person.default_permissions', []));
    }

    /**
     * @return array<int, string>
     */
    public static function personDefaultRoles(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();

        return self::normalizeList(data_get($tenant?->settings, 'access.person.default_roles', []));
    }

    /**
     * @return array<string, string>
     */
    public static function roleOptionMap(?Tenant $tenant = null): array
    {
        return collect(self::roleOptions($tenant))
            ->mapWithKeys(fn (string $role) => [$role => $role])
            ->all();
    }

    public static function companyEnforced(?Tenant $tenant = null): bool
    {
        $tenant ??= TenantContext::getTenant();

        return (bool) data_get($tenant?->settings, 'access.company.enforce', false);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function packageProfiles(?Tenant $tenant = null): array
    {
        $tenant ??= TenantContext::getTenant();

        return Arr::wrap(data_get($tenant?->settings, 'access.packages', []));
    }

    /**
     * @return array<string, string>
     */
    public static function packageOptions(?Tenant $tenant = null): array
    {
        $options = [];

        foreach (self::packageProfiles($tenant) as $package) {
            if (! is_array($package)) {
                continue;
            }
            $key = (string) ($package['key'] ?? '');
            if ($key === '') {
                continue;
            }
            $options[$key] = (string) ($package['title'] ?? $key);
        }

        return $options;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findPackage(?Tenant $tenant, string $key): ?array
    {
        foreach (self::packageProfiles($tenant) as $package) {
            if (is_array($package) && ($package['key'] ?? null) === $key) {
                return $package;
            }
        }

        return null;
    }

    /**
     * @param  array<string, array<string, string>>  $options
     * @param  array<int, string>  $allowed
     * @return array<string, array<string, string>>
     */
    public static function filterOptions(array $options, array $allowed): array
    {
        if ($allowed === []) {
            return $options;
        }

        $allowedMap = array_fill_keys($allowed, true);
        $filtered = [];

        foreach ($options as $group => $items) {
            $items = array_intersect_key($items, $allowedMap);
            if ($items !== []) {
                $filtered[$group] = $items;
            }
        }

        return $filtered;
    }

    /**
     * @param  array<int, string>  $permissions
     * @return array<string, array<string, string>>
     */
    protected static function groupPermissions(array $permissions): array
    {
        $grouped = [];
        foreach ($permissions as $permission) {
            $groupKey = self::groupKey($permission);
            $label = self::groupLabel($groupKey);
            $grouped[$label] ??= [];
            $grouped[$label][$permission] = PermissionLabels::labelWithKey($permission);
        }

        ksort($grouped);

        return $grouped;
    }

    protected static function groupLabel(string $prefix): string
    {
        return match ($prefix) {
            'iam' => 'دسترسی',
            'user' => 'کاربران',
            'role' => 'نقش‌ها',
            'permission' => 'مجوزها',
            'group' => 'گروه‌ها',
            'permission_template' => 'قالب‌های مجوز',
            'permission_override' => 'بازنویسی مجوز',
            'tenant' => 'فضای کاری',
            'organization' => 'سازمان',
            'wallet' => 'کیف پول',
            'wallet_transaction' => 'تراکنش‌ها',
            'wallet_hold' => 'هولدها',
            'subscription' => 'اشتراک',
            'subscription_plan' => 'پلن‌ها',
            'notification' => 'اعلان',
            'webhook' => 'وبهوک',
            'api' => 'API',
            'api_key' => 'کلیدهای API',
            'api_docs' => 'مستندات API',
            'access_request' => 'درخواست دسترسی',
            'permission_snapshot' => 'اسنپ‌شات دسترسی',
            'delegated_admin' => 'ادمین تفویض‌شده',
            'security' => 'امنیت',
            'audit' => 'ممیزی',
            'settings' => 'تنظیمات',
            default => $prefix,
        };
    }

    protected static function groupKey(string $permission): string
    {
        $parts = explode('.', $permission);
        $first = $parts[0] ?? 'other';
        $second = $parts[1] ?? null;

        if ($first === 'api' && $second === 'docs') {
            return 'api_docs';
        }

        if ($first === 'api' && $second === 'key') {
            return 'api_key';
        }

        return $first;
    }

    /**
     * @return array<int, string>
     */
    protected static function normalizeList(mixed $value): array
    {
        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }

        return array_values(array_filter(Arr::wrap($value)));
    }
}
