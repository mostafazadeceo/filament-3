<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\ChartAccount;

class UpdateChartAccountRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $account = $this->route('chart_account');

        return $account instanceof ChartAccount
            ? auth()->user()?->can('update', $account)
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
            'plan_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_account_plans', 'id'),
            ],
            'type_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_account_types', 'id'),
            ],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_chart_accounts', 'id'),
            ],
            'code' => ['sometimes', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:255'],
            'level' => ['nullable', 'integer', 'min:1'],
            'is_postable' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'requires_dimensions' => ['nullable', 'array'],
        ];
    }
}
