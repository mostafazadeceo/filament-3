<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
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
        $projectId = $this->route('project')?->getKey();

        return [
            'key' => [
                'required',
                'string',
                'max:16',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('workhub_projects', 'key')
                    ->where(fn ($query) => $tenantId ? $query->where('tenant_id', $tenantId) : $query)
                    ->ignore($projectId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'archived'])],
            'workflow_id' => ['required', 'integer', 'exists:workhub_workflows,id'],
            'lead_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
