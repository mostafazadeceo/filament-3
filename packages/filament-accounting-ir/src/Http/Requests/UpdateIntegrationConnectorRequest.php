<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateIntegrationConnectorRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('update', $this->route('integration_connector')) ?? false;
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
            'connector_type' => ['nullable', 'string', 'max:32'],
            'schedule' => ['nullable', 'string', 'max:64'],
            'is_active' => ['boolean'],
            'config' => ['nullable', 'array'],
        ];
    }
}
