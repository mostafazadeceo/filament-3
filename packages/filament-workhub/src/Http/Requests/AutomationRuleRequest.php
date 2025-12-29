<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Haida\FilamentWorkhub\Support\AutomationRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutomationRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $registry = app(AutomationRegistry::class);
        $triggerTypes = array_keys($registry->triggerOptions());
        $conditionTypes = array_keys($registry->conditionOptions());
        $actionTypes = array_keys($registry->actionOptions());

        return [
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:workhub_projects,id'],
            'trigger_type' => ['required', Rule::in($triggerTypes)],
            'trigger_config' => ['nullable', 'array'],
            'conditions' => ['nullable', 'array'],
            'conditions.*.type' => ['required_with:conditions', Rule::in($conditionTypes)],
            'conditions.*.config' => ['nullable', 'array'],
            'actions' => ['nullable', 'array'],
            'actions.*.type' => ['required_with:actions', Rule::in($actionTypes)],
            'actions.*.config' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
