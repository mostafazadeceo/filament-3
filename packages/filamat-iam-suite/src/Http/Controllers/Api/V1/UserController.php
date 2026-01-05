<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Services\ModuleCatalog;
use Filamat\IamSuite\Services\OrganizationEntitlementService;
use Filamat\IamSuite\Services\RoleTemplateService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends BaseController
{
    protected function modelClass(): string
    {
        return config('auth.providers.users.model');
    }

    protected function validationRules(string $action): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => $action === 'store' ? ['required', 'string', 'min:6'] : ['nullable', 'string', 'min:6'],
            'tenant_role' => ['nullable', 'string', 'in:owner,admin,member'],
            'tenant_status' => ['nullable', 'string', 'in:active,invited,inactive'],
        ];
    }

    public function store(Request $request, ?int $parentId = null): Response
    {
        $model = $this->modelClass();
        $data = $request->validate($this->validationRules('store'));

        $user = $model::query()->create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => bcrypt($data['password']),
        ]);

        $tenant = TenantContext::getTenant();
        if ($tenant && method_exists($user, 'tenants')) {
            $entitlements = app(OrganizationEntitlementService::class);
            if (! $entitlements->canInviteUser($tenant)) {
                return response([
                    'message' => 'سقف کاربران سازمان پر شده است.',
                ], 403);
            }

            $pivotRole = $data['tenant_role'] ?? 'member';
            $pivotStatus = $data['tenant_status'] ?? 'active';

            $user->tenants()->syncWithoutDetaching([
                $tenant->getKey() => [
                    'role' => $pivotRole,
                    'status' => $pivotStatus,
                    'joined_at' => now(),
                ],
            ]);

            $modules = $entitlements->allowedModulesForTenant($tenant);
            $permissions = app(ModuleCatalog::class)->permissionsForModules($modules);
            app(RoleTemplateService::class)->syncTemplatesForTenant($tenant, $permissions);
            app(RoleTemplateService::class)->assignRoleForPivot($tenant, $user, $pivotRole);
        }

        return response(['data' => $user], 201);
    }

    public function update(Request $request, int $id): Response
    {
        $model = $this->modelClass();
        $data = $request->validate($this->validationRules('update'));

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user = $this->query()->findOrFail($id);
        $user->update($data);

        return response(['data' => $user], 200);
    }
}
