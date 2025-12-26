<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    protected function modelClass(): string
    {
        return Role::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'tenant_id' => ['nullable', 'integer'],
        ];
    }
}
