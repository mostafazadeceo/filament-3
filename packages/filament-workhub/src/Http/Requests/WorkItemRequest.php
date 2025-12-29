<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkItemRequest extends FormRequest
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
            'project_id' => [$required, 'integer', 'exists:workhub_projects,id'],
            'work_type_id' => ['nullable', 'integer', 'exists:workhub_work_types,id'],
            'workflow_id' => ['nullable', 'integer', 'exists:workhub_workflows,id'],
            'status_id' => ['nullable', 'integer', 'exists:workhub_statuses,id'],
            'title' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'reporter_id' => ['nullable', 'integer', 'exists:users,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'estimate_minutes' => ['nullable', 'integer', 'min:0'],
            'labels' => ['nullable', 'array'],
            'labels.*' => ['integer', 'exists:workhub_labels,id'],
        ];
    }
}
