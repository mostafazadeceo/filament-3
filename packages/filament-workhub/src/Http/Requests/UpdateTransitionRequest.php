<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workflow_id' => ['sometimes', 'exists:workhub_workflows,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'from_status_id' => ['sometimes', 'exists:workhub_statuses,id'],
            'to_status_id' => ['sometimes', 'exists:workhub_statuses,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'validators' => ['nullable', 'array'],
            'post_actions' => ['nullable', 'array'],
        ];
    }
}
