<?php

namespace Haida\FilamentPettyCashIr\Http\Requests;

use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends BasePettyCashRequest
{
    public function authorize(): bool
    {
        $category = $this->route('category');

        return auth()->user()?->can('update', $category ?? PettyCashCategory::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'tenant_id' => ['nullable', 'integer'],
            'company_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_companies', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'accounting_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_chart_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
