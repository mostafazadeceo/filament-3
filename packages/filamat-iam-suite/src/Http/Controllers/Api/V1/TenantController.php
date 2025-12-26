<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Tenant;

class TenantController extends BaseController
{
    protected function modelClass(): string
    {
        return Tenant::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'organization_id' => ['nullable', 'integer'],
            'owner_user_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string'],
            'locale' => ['nullable', 'string'],
            'timezone' => ['nullable', 'string'],
        ];
    }
}
