<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Validation\Rule;

class SubscriptionController extends BaseController
{
    protected function modelClass(): string
    {
        return Subscription::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'tenant_id' => [Rule::requiredIf(TenantContext::shouldBypass()), 'integer'],
            'user_id' => ['nullable', 'integer'],
            'plan_id' => ['required', 'integer'],
            'status' => ['nullable', 'string'],
            'trial_ends_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'renews_at' => ['nullable', 'date'],
            'provider' => ['nullable', 'string'],
            'provider_ref' => ['nullable', 'string'],
        ];
    }
}
