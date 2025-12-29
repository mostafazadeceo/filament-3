<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $projectId = $this->route('project')?->getKey();
        $linkTypes = $this->availableLinkTypes();

        return [
            'key' => [
                'sometimes',
                'max:16',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('workhub_projects', 'key')->where('tenant_id', $tenantId)->ignore($projectId),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'workflow_id' => ['sometimes', 'exists:workhub_workflows,id'],
            'status' => ['sometimes', Rule::in(['active', 'archived'])],
            'lead_user_id' => ['nullable', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'allowed_link_types' => ['nullable', 'array'],
            'allowed_link_types.*' => ['string', Rule::in($linkTypes)],
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function availableLinkTypes(): array
    {
        $registry = app(EntityReferenceRegistry::class);

        return collect($registry->all())
            ->pluck('type')
            ->values()
            ->all();
    }
}
