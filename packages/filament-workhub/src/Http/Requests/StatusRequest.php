<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusRequest extends FormRequest
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
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'workflow_id' => [$required, 'integer', 'exists:workhub_workflows,id'],
            'name' => [$required, 'string', 'max:255'],
            'slug' => [$required, 'string', 'max:255'],
            'category' => [$required, Rule::in(['todo', 'in_progress', 'done'])],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $workflowId = $this->input('workflow_id') ?? $this->route('status')?->workflow_id;
            if (! $workflowId) {
                return;
            }

            $tenantId = TenantContext::getTenantId();
            if (! $tenantId) {
                return;
            }

            $workflowExists = Workflow::query()
                ->whereKey($workflowId)
                ->where('tenant_id', $tenantId)
                ->exists();

            if (! $workflowExists) {
                $validator->errors()->add('workflow_id', 'گردش‌کار انتخاب‌شده معتبر نیست.');
            }
        });
    }
}
