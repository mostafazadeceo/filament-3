<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\RegisteredCapability;
use Illuminate\Support\Str;

class ModuleCatalog
{
    /** @var array<string, array<string, mixed>> */
    protected array $modules = [];

    /** @var array<string, string> */
    protected array $permissionMap = [];

    protected bool $built = false;

    public function __construct(protected CapabilityRegistryInterface $registry) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function modules(): array
    {
        $this->build();

        return $this->modules;
    }

    /**
     * @return array<string, string>
     */
    public function moduleOptions(): array
    {
        $this->build();

        return collect($this->modules)
            ->mapWithKeys(fn (array $module) => [$module['key'] => $module['label']])
            ->all();
    }

    public function moduleForPermission(string $permission): ?string
    {
        $this->build();

        return $this->permissionMap[$permission] ?? null;
    }

    /**
     * @param  array<int, string>  $modules
     * @return array<int, string>
     */
    public function permissionsForModules(array $modules): array
    {
        $this->build();

        $modules = array_values(array_filter($modules));
        if ($modules === []) {
            return [];
        }

        $permissions = [];
        foreach ($modules as $moduleKey) {
            $module = $this->modules[$moduleKey] ?? null;
            if (! $module) {
                continue;
            }

            $permissions = array_merge($permissions, $module['permissions']);
        }

        $permissions = array_values(array_unique(array_filter($permissions)));
        sort($permissions);

        return $permissions;
    }

    /**
     * @param  array<int, string>  $permissions
     * @param  array<int, string>  $modules
     * @return array<int, string>
     */
    public function filterPermissionsByModules(array $permissions, array $modules): array
    {
        $this->build();

        $allowedModules = array_values(array_filter($modules));
        if ($allowedModules === []) {
            return $permissions;
        }

        $allowedMap = array_fill_keys($allowedModules, true);

        return array_values(array_filter($permissions, function (string $permission) use ($allowedMap): bool {
            $module = $this->permissionMap[$permission] ?? null;
            if (! $module) {
                return false;
            }

            return isset($allowedMap[$module]);
        }));
    }

    protected function build(): void
    {
        if ($this->built) {
            return;
        }

        $modules = [];
        $permissionMap = [];

        foreach ($this->registry->all() as $capability) {
            if (! $capability instanceof RegisteredCapability) {
                continue;
            }

            $label = $this->resolveLabel($capability);
            $modules[$capability->module] = [
                'key' => $capability->module,
                'label' => $label,
                'permissions' => array_values(array_unique($capability->permissions)),
                'feature_flags' => $capability->featureFlags,
                'quotas' => $capability->quotas,
                'navigation' => $capability->navigation,
            ];

            foreach ($capability->permissions as $permission) {
                $permissionMap[$permission] = $capability->module;
            }
        }

        ksort($modules);

        $this->modules = $modules;
        $this->permissionMap = $permissionMap;
        $this->built = true;
    }

    protected function resolveLabel(RegisteredCapability $capability): string
    {
        $label = (string) (config('filamat-iam.modules.labels.'.$capability->module) ?? '');
        if ($label !== '') {
            return $label;
        }

        $navigation = $capability->navigation;
        if (is_array($navigation) && $navigation !== []) {
            $first = reset($navigation);
            if (is_string($first) && $first !== '') {
                return $first;
            }
        }

        return Str::headline(str_replace(['filament-', 'haida-'], '', $capability->module));
    }
}
