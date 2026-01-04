<?php

namespace Haida\FilamentWorkhub\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentWorkhub\Policies\AttachmentPolicy;
use Haida\FilamentWorkhub\Policies\AuditEventPolicy;
use Haida\FilamentWorkhub\Policies\AutomationRulePolicy;
use Haida\FilamentWorkhub\Policies\CommentPolicy;
use Haida\FilamentWorkhub\Policies\CustomFieldPolicy;
use Haida\FilamentWorkhub\Policies\DecisionPolicy;
use Haida\FilamentWorkhub\Policies\EntityReferencePolicy;
use Haida\FilamentWorkhub\Policies\LabelPolicy;
use Haida\FilamentWorkhub\Policies\ProjectPolicy;
use Haida\FilamentWorkhub\Policies\StatusPolicy;
use Haida\FilamentWorkhub\Policies\TimeEntryPolicy;
use Haida\FilamentWorkhub\Policies\TransitionPolicy;
use Haida\FilamentWorkhub\Policies\WatcherPolicy;
use Haida\FilamentWorkhub\Policies\WorkflowPolicy;
use Haida\FilamentWorkhub\Policies\WorkItemPolicy;
use Haida\FilamentWorkhub\Policies\WorkTypePolicy;

final class WorkhubCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-workhub',
            self::permissions(),
            [
                'workhub' => true,
            ],
            [],
            [
                ProjectPolicy::class,
                WorkItemPolicy::class,
                WorkflowPolicy::class,
                StatusPolicy::class,
                TransitionPolicy::class,
                CommentPolicy::class,
                AttachmentPolicy::class,
                WatcherPolicy::class,
                TimeEntryPolicy::class,
                DecisionPolicy::class,
                AuditEventPolicy::class,
                LabelPolicy::class,
                EntityReferencePolicy::class,
                WorkTypePolicy::class,
                CustomFieldPolicy::class,
                AutomationRulePolicy::class,
            ],
            [
                'workhub' => 'رهگیری کارها',
                'workhub_projects' => 'پروژه‌ها',
                'workhub_items' => 'آیتم‌های کاری',
                'workhub_workflows' => 'گردش‌کارها',
                'workhub_statuses' => 'وضعیت‌ها',
                'workhub_transitions' => 'انتقال‌ها',
                'workhub_work_types' => 'نوع‌های کار',
                'workhub_custom_fields' => 'فیلدهای سفارشی',
                'workhub_automation' => 'اتوماسیون',
                'workhub_ai' => 'هوش مصنوعی',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'workhub.project.view',
            'workhub.project.manage',
            'workhub.work_item.view',
            'workhub.work_item.manage',
            'workhub.workflow.view',
            'workhub.workflow.manage',
            'workhub.status.view',
            'workhub.status.manage',
            'workhub.transition.view',
            'workhub.transition.manage',
            'workhub.work_type.view',
            'workhub.work_type.manage',
            'workhub.comment.view',
            'workhub.comment.manage',
            'workhub.attachment.view',
            'workhub.attachment.manage',
            'workhub.watcher.view',
            'workhub.watcher.manage',
            'workhub.label.view',
            'workhub.label.manage',
            'workhub.time_entry.view',
            'workhub.time_entry.manage',
            'workhub.decision.view',
            'workhub.decision.manage',
            'workhub.audit.view',
            'workhub.automation.view',
            'workhub.automation.manage',
            'workhub.custom_field.view',
            'workhub.custom_field.manage',
            'workhub.link.view',
            'workhub.link.manage',
            'workhub.ai.use',
            'workhub.ai.share',
            'workhub.ai.fields.manage',
            'workhub.ai.project_reports.manage',
        ];
    }
}
