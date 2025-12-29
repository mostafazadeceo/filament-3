<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $recordId = $this->route('custom_field')?->getKey();
        $scope = $this->input('scope');

        return [
            'scope' => ['required', Rule::in(['work_item', 'project'])],
            'name' => ['required', 'string', 'max:255'],
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('workhub_custom_fields', 'key')
                    ->where('tenant_id', $tenantId)
                    ->where('scope', $scope)
                    ->ignore($recordId),
            ],
            'type' => ['required', Rule::in(['text', 'textarea', 'number', 'date', 'boolean', 'select', 'multi_select'])],
            'settings' => ['nullable', 'array'],
            'settings.options' => ['nullable', 'array'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
