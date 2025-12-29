<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Foundation\Http\FormRequest;

class TransitionRequest extends FormRequest
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
            'from_status_id' => [$required, 'integer', 'exists:workhub_statuses,id'],
            'to_status_id' => [$required, 'integer', 'exists:workhub_statuses,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'validators' => ['nullable', 'array'],
            'post_actions' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $workflowId = $this->input('workflow_id') ?? $this->route('transition')?->workflow_id;
            if (! $workflowId) {
                return;
            }

            $tenantId = TenantContext::getTenantId();
            if ($tenantId) {
                $workflowExists = Workflow::query()
                    ->whereKey($workflowId)
                    ->where('tenant_id', $tenantId)
                    ->exists();

                if (! $workflowExists) {
                    $validator->errors()->add('workflow_id', 'گردش‌کار انتخاب‌شده معتبر نیست.');

                    return;
                }
            }

            foreach (['from_status_id', 'to_status_id'] as $field) {
                if (! $this->filled($field)) {
                    continue;
                }

                $statusId = (int) $this->input($field);
                $statusExists = Status::query()
                    ->where('workflow_id', $workflowId)
                    ->whereKey($statusId)
                    ->exists();

                if (! $statusExists) {
                    $validator->errors()->add($field, 'وضعیت انتخاب‌شده معتبر نیست.');
                }
            }
        });
    }
}
