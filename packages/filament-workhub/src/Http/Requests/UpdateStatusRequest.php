<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusId = $this->route('status')?->getKey();
        $workflowId = $this->input('workflow_id') ?? $this->route('status')?->workflow_id;

        return [
            'workflow_id' => ['sometimes', 'exists:workhub_workflows,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('workhub_statuses', 'slug')->where('workflow_id', $workflowId)->ignore($statusId),
            ],
            'category' => ['sometimes', Rule::in(['todo', 'in_progress', 'done'])],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
