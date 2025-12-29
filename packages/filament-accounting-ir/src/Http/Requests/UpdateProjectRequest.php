<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\Project;

class UpdateProjectRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project
            ? auth()->user()?->can('update', $project)
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
            'code' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'string', 'max:32'],
        ];
    }
}
