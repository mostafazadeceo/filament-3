<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\AccountPlan;

class UpdateAccountPlanRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $plan = $this->route('account_plan');

        return $plan instanceof AccountPlan
            ? auth()->user()?->can('update', $plan)
            : false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'company_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_companies', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
        ];
    }
}
