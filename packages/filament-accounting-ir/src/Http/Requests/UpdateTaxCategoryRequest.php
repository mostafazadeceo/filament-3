<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateTaxCategoryRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('update', $this->route('tax_category')) ?? false;
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
            'code' => ['sometimes', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:255'],
            'vat_rate' => ['nullable', 'numeric', 'min:0'],
            'is_exempt' => ['boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
