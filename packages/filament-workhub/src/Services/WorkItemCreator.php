<?php

namespace Haida\FilamentWorkhub\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Models\WorkType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkItemCreator
{
    public function create(array $data): WorkItem
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'] ?? TenantContext::getTenantId();
            if ($tenantId) {
                $data['tenant_id'] = $tenantId;
            }

            if (! array_key_exists('reporter_id', $data) || ! $data['reporter_id']) {
                $data['reporter_id'] = auth()->id();
            }

            if (! array_key_exists('created_by', $data)) {
                $data['created_by'] = auth()->id();
            }

            if (! array_key_exists('updated_by', $data)) {
                $data['updated_by'] = auth()->id();
            }

            $project = Project::query()
                ->lockForUpdate()
                ->findOrFail($data['project_id']);

            $workflowId = $project->workflow_id;
            $data['workflow_id'] = $workflowId;

            if (array_key_exists('work_type_id', $data) && $data['work_type_id']) {
                $workTypeId = (int) $data['work_type_id'];
                $workTypeExists = WorkType::query()
                    ->where('tenant_id', $project->tenant_id)
                    ->whereKey($workTypeId)
                    ->exists();

                if (! $workTypeExists) {
                    throw ValidationException::withMessages([
                        'work_type_id' => 'نوع کار انتخاب‌شده معتبر نیست.',
                    ]);
                }
            }

            $statusId = $data['status_id'] ?? $this->resolveDefaultStatusId($workflowId);
            if (! $statusId) {
                throw ValidationException::withMessages([
                    'status_id' => 'وضعیت پیش‌فرض برای گردش‌کار یافت نشد.',
                ]);
            }

            $status = Status::query()
                ->where('workflow_id', $workflowId)
                ->whereKey($statusId)
                ->first();

            if (! $status) {
                throw ValidationException::withMessages([
                    'status_id' => 'وضعیت انتخاب‌شده معتبر نیست.',
                ]);
            }

            $data['status_id'] = $status->getKey();

            $nextNumber = (int) WorkItem::query()
                ->where('project_id', $project->getKey())
                ->max('number') + 1;

            $data['number'] = Arr::get($data, 'number', $nextNumber);
            $data['key'] = Arr::get($data, 'key', strtoupper($project->key).'-'.$data['number']);

            $nextOrder = (int) WorkItem::query()
                ->where('project_id', $project->getKey())
                ->where('status_id', $statusId)
                ->max('sort_order') + 1;

            $data['sort_order'] = Arr::get($data, 'sort_order', $nextOrder);

            return WorkItem::create($data);
        });
    }

    protected function resolveDefaultStatusId(int $workflowId): ?int
    {
        $status = Status::query()
            ->where('workflow_id', $workflowId)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->first();

        return $status?->getKey();
    }
}
