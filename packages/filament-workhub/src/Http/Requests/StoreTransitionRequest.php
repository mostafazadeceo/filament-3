<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workflow_id' => ['required', 'exists:workhub_workflows,id'],
            'name' => ['required', 'string', 'max:255'],
            'from_status_id' => ['required', 'exists:workhub_statuses,id'],
            'to_status_id' => ['required', 'exists:workhub_statuses,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'validators' => ['nullable', 'array'],
            'post_actions' => ['nullable', 'array'],
        ];
    }
}
