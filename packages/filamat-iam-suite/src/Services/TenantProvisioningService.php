<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TenantProvisioningService
{
    public function __construct(
        protected ModuleCatalog $moduleCatalog,
        protected RoleTemplateService $roleTemplateService,
        protected OrganizationEntitlementService $entitlementService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $modules
     */
    public function createTenant(
        Organization $organization,
        array $data,
        Authenticatable $owner,
        array $modules = [],
        ?Authenticatable $actor = null
    ): Tenant {
        return DB::transaction(function () use ($organization, $data, $owner, $modules, $actor): Tenant {
            $name = trim((string) ($data['name'] ?? 'Tenant'));

            // `tenants.slug` is NOT NULL + UNIQUE. The wizard may not provide it, so we must generate one.
            $baseSlug = trim((string) ($data['slug'] ?? ''));
            if ($baseSlug === '') {
                $baseSlug = Str::slug($name);
            }
            if ($baseSlug === '') {
                $baseSlug = 'tenant';
            }

            $slug = $baseSlug;

            // Pre-check to get a human-friendly suffix when the slug already exists.
            if (Tenant::query()->where('slug', $slug)->exists()) {
                for ($i = 2; $i <= 20; $i++) {
                    $candidate = "{$baseSlug}-{$i}";
                    if (! Tenant::query()->where('slug', $candidate)->exists()) {
                        $slug = $candidate;
                        break;
                    }
                }

                if (Tenant::query()->where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-" . Str::lower(Str::random(6));
                }
            }

            // Race-safe: retry on unique constraint violation.
            $tenant = null;
            for ($attempt = 0; $attempt < 5; $attempt++) {
                try {
                    $tenant = Tenant::query()->create([
                        'name' => $name,
                        'slug' => $slug,
                        'organization_id' => $organization->getKey(),
                        'owner_user_id' => $owner->getAuthIdentifier(),
                        'status' => $data['status'] ?? 'active',
                        'locale' => $data['locale'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'settings' => $data['settings'] ?? [],
                    ]);
                    break;
                } catch (QueryException $e) {
                    // PostgreSQL unique violation: 23505.
                    if (($e->errorInfo[0] ?? null) !== '23505') {
                        throw $e;
                    }

                    // Next attempt: random suffix to guarantee uniqueness.
                    $slug = "{$baseSlug}-" . Str::lower(Str::random(8));
                }
            }

            if (! $tenant instanceof Tenant) {
                throw new \RuntimeException('Failed to create tenant after slug retries.');
            }

            $this->finalizeTenant($tenant, $owner, $modules, $actor);

            return $tenant;
        });
    }

    /**
     * @param  array<int, string>  $modules
     */
    public function finalizeTenant(
        Tenant $tenant,
        Authenticatable $owner,
        array $modules = [],
        ?Authenticatable $actor = null
    ): Tenant {
        $modules = $this->normalizeModules($tenant, $modules);
        $permissions = $this->moduleCatalog->permissionsForModules($modules);

        $settings = (array) ($tenant->settings ?? []);
        data_set($settings, 'access.modules', $modules);
        $tenant->settings = $settings;
        $tenant->save();

        $this->roleTemplateService->syncTemplatesForTenant($tenant, $permissions);
        $this->attachOwner($tenant, $owner, $actor);
        $this->roleTemplateService->assignRoleForPivot($tenant, $owner, 'owner');

        return $tenant;
    }

    public function syncOwner(Tenant $tenant, ?Authenticatable $actor = null): void
    {
        $ownerId = $tenant->owner_user_id;
        if (! $ownerId) {
            return;
        }

        $userModel = config('auth.providers.users.model');
        $owner = $userModel::query()->find($ownerId);
        if (! $owner) {
            return;
        }

        $this->attachOwner($tenant, $owner, $actor);
        $this->roleTemplateService->assignRoleForPivot($tenant, $owner, 'owner');
    }

    protected function attachOwner(Tenant $tenant, Authenticatable $owner, ?Authenticatable $actor = null): void
    {
        if (! method_exists($owner, 'tenants')) {
            return;
        }

        $owner->tenants()->syncWithoutDetaching([
            $tenant->getKey() => [
                'role' => 'owner',
                'status' => 'active',
                'joined_at' => now(),
                'activated_at' => now(),
                'activated_by_id' => $actor?->getAuthIdentifier() ?? $owner->getAuthIdentifier(),
            ],
        ]);
    }

    /**
     * @param  array<int, string>  $modules
     * @return array<int, string>
     */
    protected function normalizeModules(Tenant $tenant, array $modules): array
    {
        $modules = array_values(array_unique(array_filter($modules)));
        if ($modules === []) {
            $modules = $this->entitlementService->allowedModulesForTenant($tenant);
        }

        if (! in_array('filamat-iam-suite', $modules, true)) {
            $modules[] = 'filamat-iam-suite';
        }

        sort($modules);

        return $modules;
    }
}
