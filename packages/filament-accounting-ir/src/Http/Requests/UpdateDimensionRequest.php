<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\Dimension;

class UpdateDimensionRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $dimension = $this->route('dimension');

        return $dimension instanceof Dimension
            ? auth()->user()?->can('update', $dimension)
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
            'code' => ['sometimes', 'string', 'max:64'],
            'is_active' => ['boolean'],
        ];
    }
}
