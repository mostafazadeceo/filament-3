<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LabelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $labelId = $this->route('label')?->getKey();

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('workhub_labels', 'slug')
                    ->where(fn ($query) => $tenantId ? $query->where('tenant_id', $tenantId) : $query)
                    ->ignore($labelId),
            ],
            'color' => ['nullable', 'string', 'max:20'],
        ];
    }
}
