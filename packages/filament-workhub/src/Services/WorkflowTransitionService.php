<?php

namespace Haida\FilamentWorkhub\Services;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\DTOs\TransitionDto;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Haida\FilamentWorkhub\Events\WorkItemTransitioned;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowTransitionService
{
    public function transition(WorkItem $item, Status $toStatus, array $context = [], bool $bypassAuthorization = false): WorkItem
    {
        if (! $bypassAuthorization && ! IamAuthorization::allows('workhub.transition.manage', IamAuthorization::resolveTenantFromRecord($item))) {
            throw ValidationException::withMessages(['authorization' => 'اجازه انتقال وضعیت ندارید.']);
        }

        if ($item->workflow_id !== $toStatus->workflow_id) {
            throw ValidationException::withMessages(['status_id' => 'وضعیت انتخاب‌شده با گردش‌کار آیتم همخوان نیست.']);
        }

        $transition = Transition::query()
            ->where('workflow_id', $item->workflow_id)
            ->where('from_status_id', $item->status_id)
            ->where('to_status_id', $toStatus->getKey())
            ->where('is_active', true)
            ->first();

        if (! $transition) {
            throw ValidationException::withMessages(['status_id' => 'انتقال مجاز نیست.']);
        }

        $this->validateTransition($item, $transition, $context);

        return DB::transaction(function () use ($item, $toStatus, $transition) {
            $item->status_id = $toStatus->getKey();
            $item->updated_by = auth()->id();

            if ($toStatus->category === 'done') {
                $item->completed_at = now();
            } elseif ($toStatus->category === 'in_progress' && ! $item->started_at) {
                $item->started_at = now();
                $item->completed_at = null;
            } else {
                $item->completed_at = null;
            }

            $item->sort_order = (int) WorkItem::query()
                ->where('project_id', $item->project_id)
                ->where('status_id', $toStatus->getKey())
                ->max('sort_order') + 1;

            $item->save();

            app(WorkhubAuditService::class)->log('work_item.transitioned', null, $item, [
                'from_status_id' => $transition->from_status_id,
                'to_status_id' => $transition->to_status_id,
                'transition_id' => $transition->getKey(),
            ]);

            event(new WorkItemTransitioned(
                WorkItemDto::fromModel($item),
                TransitionDto::fromModel($transition)
            ));
            return $item;
        });
    }

    protected function validateTransition(WorkItem $item, Transition $transition, array $context = []): void
    {
        $validators = (array) ($transition->validators ?? []);

        if (($validators['requires_assignee'] ?? false) && ! $item->assignee_id) {
            throw ValidationException::withMessages(['assignee_id' => 'برای این انتقال باید مسئول تعیین شود.']);
        }

        if (($validators['requires_due_date'] ?? false) && ! $item->due_date) {
            throw ValidationException::withMessages(['due_date' => 'برای این انتقال باید تاریخ سررسید ثبت شود.']);
        }

        unset($context);
    }
}
