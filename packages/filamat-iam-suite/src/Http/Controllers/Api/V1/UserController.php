<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

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
            'tenant_role' => ['nullable', 'string'],
            'tenant_status' => ['nullable', 'string'],
        ];
    }

    public function store(Request $request): Response
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
            $user->tenants()->syncWithoutDetaching([
                $tenant->getKey() => [
                    'role' => $data['tenant_role'] ?? 'member',
                    'status' => $data['tenant_status'] ?? 'active',
                    'joined_at' => now(),
                ],
            ]);
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
