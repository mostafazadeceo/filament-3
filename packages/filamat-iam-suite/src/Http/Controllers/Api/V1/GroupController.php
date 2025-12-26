<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Group;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Validation\Rule;

class GroupController extends BaseController
{
    protected function modelClass(): string
    {
        return Group::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'tenant_id' => [Rule::requiredIf(TenantContext::shouldBypass()), 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
