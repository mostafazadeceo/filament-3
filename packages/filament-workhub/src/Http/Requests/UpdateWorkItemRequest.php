<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\Label;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\WorkType;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\CustomFieldManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateWorkItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_type_id' => ['nullable', 'exists:workhub_work_types,id'],
            'status_id' => ['nullable', 'exists:workhub_statuses,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', Rule::in(array_keys(config('filament-workhub.work_item.priorities', [])))],
            'reporter_id' => ['nullable', 'exists:users,id'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'estimate_minutes' => ['nullable', 'integer', 'min:0'],
            'labels' => ['nullable', 'array'],
            'labels.*' => ['integer', 'exists:workhub_labels,id'],
            'custom_fields' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $values = $this->input('custom_fields', null);
            if ($values !== null && ! is_array($values)) {
                $values = [];
            }

            $tenantId = TenantContext::getTenantId();
            $workItem = $this->route('workItem');
            if (! $tenantId && $workItem) {
                $tenantId = $workItem->tenant_id;
            }
            if (! $tenantId && $this->input('project_id')) {
                $tenantId = Project::query()->whereKey($this->input('project_id'))->value('tenant_id');
            }

            if (! $tenantId) {
                return;
            }

            if (is_array($values)) {
                [, $errors] = app(CustomFieldManager::class)->validateValues('work_item', $values, (int) $tenantId, false);

                foreach ($errors as $key => $message) {
                    $validator->errors()->add('custom_fields.'.$key, $message);
                }
            }

            if ($workItem instanceof WorkItem && $this->filled('status_id')) {
                $statusId = (int) $this->input('status_id');
                $statusExists = Status::query()
                    ->where('workflow_id', $workItem->workflow_id)
                    ->whereKey($statusId)
                    ->exists();

                if (! $statusExists) {
                    $validator->errors()->add('status_id', 'وضعیت انتخاب‌شده معتبر نیست.');
                }
            }

            if ($this->filled('work_type_id')) {
                $workTypeId = (int) $this->input('work_type_id');
                $workTypeExists = WorkType::query()
                    ->where('tenant_id', $tenantId)
                    ->whereKey($workTypeId)
                    ->exists();

                if (! $workTypeExists) {
                    $validator->errors()->add('work_type_id', 'نوع کار انتخاب‌شده معتبر نیست.');
                }
            }

            $labelIds = array_values(array_filter((array) $this->input('labels', [])));
            if ($labelIds !== []) {
                $validCount = Label::query()
                    ->where('tenant_id', $tenantId)
                    ->whereIn('id', $labelIds)
                    ->count();

                if ($validCount !== count($labelIds)) {
                    $validator->errors()->add('labels', 'یکی از برچسب‌ها معتبر نیست.');
                }
            }

            foreach (['reporter_id', 'assignee_id'] as $field) {
                if (! $this->filled($field)) {
                    continue;
                }

                $userId = (int) $this->input($field);
                $memberExists = DB::table('tenant_user')
                    ->where('tenant_id', $tenantId)
                    ->where('user_id', $userId)
                    ->exists();

                if (! $memberExists) {
                    $validator->errors()->add($field, 'کاربر باید عضو همین فضای کاری باشد.');
                }
            }
        });
    }
}
