<?php

namespace Haida\FilamentWorkhub\Support;

use Haida\FilamentWorkhub\Models\Comment;
use Haida\FilamentWorkhub\Models\Label;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkflowTransitionService;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WorkhubAutomationDefaults
{
    public static function register(AutomationRegistry $registry): void
    {
        $registry->registerTrigger('work_item.created', 'ایجاد آیتم کاری');
        $registry->registerTrigger('work_item.updated', 'به‌روزرسانی آیتم کاری');
        $registry->registerTrigger('work_item.transitioned', 'انتقال آیتم کاری');
        $registry->registerTrigger('project.created', 'ایجاد پروژه');
        $registry->registerTrigger('project.updated', 'به‌روزرسانی پروژه');
        $registry->registerTrigger('comment.created', 'ایجاد دیدگاه');
        $registry->registerTrigger('attachment.created', 'افزودن پیوست');
        $registry->registerTrigger('schedule', 'زمان‌بندی');

        $registry->registerCondition('status_is', 'وضعیت برابر است با', function (AutomationContext $context, array $config): bool {
            $statusId = (int) ($config['status_id'] ?? 0);

            return $statusId !== 0 && (int) $context->get('work_item.status_id') === $statusId;
        });

        $registry->registerCondition('priority_is', 'اولویت برابر است با', function (AutomationContext $context, array $config): bool {
            $priority = (string) ($config['priority'] ?? '');

            return $priority !== '' && (string) $context->get('work_item.priority') === $priority;
        });

        $registry->registerCondition('assignee_is', 'مسئول برابر است با', function (AutomationContext $context, array $config): bool {
            $assigneeId = (int) ($config['assignee_id'] ?? 0);

            return $assigneeId !== 0 && (int) $context->get('work_item.assignee_id') === $assigneeId;
        });

        $registry->registerCondition('project_is', 'پروژه برابر است با', function (AutomationContext $context, array $config): bool {
            $projectId = (int) ($config['project_id'] ?? 0);

            return $projectId !== 0 && (int) $context->get('work_item.project_id') === $projectId;
        });

        $registry->registerCondition('has_label', 'برچسب دارد', function (AutomationContext $context, array $config): bool {
            $labelId = (int) ($config['label_id'] ?? 0);
            $labels = (array) $context->get('work_item.labels', []);

            return $labelId !== 0 && in_array($labelId, $labels, true);
        });

        $registry->registerCondition('due_in_days', 'سررسید تا X روز', function (AutomationContext $context, array $config): bool {
            $days = (int) ($config['days'] ?? 0);
            $dueDate = $context->get('work_item.due_date');
            if (! $dueDate || $days === 0) {
                return false;
            }

            $target = Carbon::parse($dueDate);

            return now()->diffInDays($target, false) <= $days;
        });

        $registry->registerCondition('field_equals', 'فیلد سفارشی برابر است با', function (AutomationContext $context, array $config): bool {
            $fieldKey = (string) ($config['field_key'] ?? '');
            $expected = $config['value'] ?? null;
            if ($fieldKey === '') {
                return false;
            }

            return $context->get('work_item.custom_fields.'.$fieldKey) == $expected;
        });

        $registry->registerAction('transition_status', 'انتقال وضعیت', function (AutomationContext $context, array $config): void {
            $workItem = $context->workItem();
            if (! $workItem) {
                return;
            }

            $statusId = (int) ($config['status_id'] ?? 0);
            if ($statusId === 0) {
                return;
            }

            $status = Status::query()->find($statusId);
            if (! $status) {
                return;
            }

            app(WorkflowTransitionService::class)->transition($workItem, $status, [], true);
        });

        $registry->registerAction('assign_user', 'تخصیص مسئول', function (AutomationContext $context, array $config): void {
            $workItem = $context->workItem();
            if (! $workItem) {
                return;
            }

            $assigneeId = (int) ($config['assignee_id'] ?? 0);
            if ($assigneeId === 0) {
                return;
            }

            $workItem->assignee_id = $assigneeId;
            $workItem->updated_by = $workItem->updated_by ?? null;
            $workItem->save();

            app(WorkhubAuditService::class)->log('work_item.assigned', $workItem->project, $workItem, [
                'assignee_id' => $assigneeId,
            ]);
        });

        $registry->registerAction('set_due_date', 'تنظیم سررسید', function (AutomationContext $context, array $config): void {
            $workItem = $context->workItem();
            if (! $workItem) {
                return;
            }

            $dueDate = $config['due_date'] ?? null;
            $days = Arr::get($config, 'days_from_now');

            if ($days !== null) {
                $workItem->due_date = now()->addDays((int) $days)->toDateString();
            } elseif ($dueDate) {
                $workItem->due_date = $context->resolveValue((string) $dueDate);
            } else {
                return;
            }

            $workItem->save();

            app(WorkhubAuditService::class)->log('work_item.due_date_set', $workItem->project, $workItem, [
                'due_date' => $workItem->due_date,
            ]);
        });

        $registry->registerAction('add_label', 'افزودن برچسب', function (AutomationContext $context, array $config): void {
            $workItem = $context->workItem();
            if (! $workItem) {
                return;
            }

            $labelId = (int) ($config['label_id'] ?? 0);
            if ($labelId === 0) {
                return;
            }

            $label = Label::query()->find($labelId);
            if (! $label) {
                return;
            }

            $workItem->labels()->syncWithoutDetaching([
                $label->getKey() => ['tenant_id' => $workItem->tenant_id],
            ]);

            app(WorkhubAuditService::class)->log('work_item.label_added', $workItem->project, $workItem, [
                'label_id' => $labelId,
            ]);
        });

        $registry->registerAction('add_comment', 'افزودن دیدگاه', function (AutomationContext $context, array $config): void {
            $workItem = $context->workItem();
            if (! $workItem) {
                return;
            }

            $body = $context->resolveValue((string) ($config['body'] ?? ''));
            if (Str::of($body)->trim()->isEmpty()) {
                return;
            }

            Comment::query()->create([
                'tenant_id' => $workItem->tenant_id,
                'work_item_id' => $workItem->getKey(),
                'user_id' => null,
                'body' => $body,
                'is_internal' => (bool) ($config['is_internal'] ?? false),
            ]);
        });

        $registry->registerAction('set_priority', 'تنظیم اولویت', function (AutomationContext $context, array $config): void {
            $workItem = $context->workItem();
            if (! $workItem) {
                return;
            }

            $priority = (string) ($config['priority'] ?? '');
            if ($priority === '') {
                return;
            }

            $workItem->priority = $priority;
            $workItem->save();

            app(WorkhubAuditService::class)->log('work_item.priority_set', $workItem->project, $workItem, [
                'priority' => $priority,
            ]);
        });
    }
}
