<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\SubscriptionPlan;

class SubscriptionPlanController extends BaseController
{
    protected function modelClass(): string
    {
        return SubscriptionPlan::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'tenant_id' => ['nullable', 'integer'],
            'scope' => ['nullable', 'string'],
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
            'price' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string'],
            'period_days' => ['nullable', 'integer'],
            'trial_days' => ['nullable', 'integer'],
            'seat_limit' => ['nullable', 'integer'],
            'storage_limit' => ['nullable', 'integer'],
            'module_limit' => ['nullable', 'integer'],
            'features' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
