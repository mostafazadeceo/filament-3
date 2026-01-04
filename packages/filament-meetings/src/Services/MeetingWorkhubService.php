<?php

namespace Haida\FilamentMeetings\Services;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingActionItem;
use Haida\FilamentWorkhub\Models\Comment;
use Haida\FilamentWorkhub\Models\EntityReference;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkItemCreator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class MeetingWorkhubService
{
    public function __construct(protected WorkItemCreator $creator) {}

    /**
     * @param  array<int, int>  $actionItemIds
     * @return array{ok: bool, message?: string, linked?: int}
     */
    public function linkActionItems(Meeting $meeting, array $actionItemIds, ?Authenticatable $actor = null, ?int $projectId = null): array
    {
        $projectId = $projectId ?? (int) data_get($meeting->meta, 'workhub_project_id');
        if (! $projectId) {
            return ['ok' => false, 'message' => 'پروژه ورک‌هاب برای این جلسه مشخص نشده است.'];
        }

        $project = Project::query()
            ->where('tenant_id', $meeting->tenant_id)
            ->find($projectId);

        if (! $project) {
            return ['ok' => false, 'message' => 'پروژه انتخاب‌شده یافت نشد.'];
        }

        if (! $project->allowsLinkType('meetings.meeting')) {
            return ['ok' => false, 'message' => 'لینک کردن جلسه برای این پروژه مجاز نیست.'];
        }

        $items = MeetingActionItem::query()
            ->where('meeting_id', $meeting->getKey())
            ->whereIn('id', $actionItemIds)
            ->get();

        if ($items->isEmpty()) {
            return ['ok' => false, 'message' => 'اقدام معتبری یافت نشد.'];
        }

        $linked = 0;

        DB::transaction(function () use ($items, $meeting, $project, $actor, &$linked) {
            foreach ($items as $item) {
                if ($item->linked_workhub_item_id) {
                    continue;
                }

                /** @var WorkItem $workItem */
                $workItem = $this->creator->create([
                    'tenant_id' => $meeting->tenant_id,
                    'project_id' => $project->getKey(),
                    'title' => $item->title,
                    'description' => $item->description,
                    'priority' => $item->priority ?: 'medium',
                    'reporter_id' => $actor?->getAuthIdentifier(),
                    'created_by' => $actor?->getAuthIdentifier(),
                    'updated_by' => $actor?->getAuthIdentifier(),
                ]);

                Comment::query()->create([
                    'tenant_id' => $meeting->tenant_id,
                    'work_item_id' => $workItem->getKey(),
                    'user_id' => $actor?->getAuthIdentifier(),
                    'body' => 'ایجاد شده از جلسه '.$meeting->title,
                    'is_internal' => false,
                ]);

                EntityReference::query()->firstOrCreate([
                    'tenant_id' => $meeting->tenant_id,
                    'source_type' => WorkItem::class,
                    'source_id' => $workItem->getKey(),
                    'target_type' => Meeting::class,
                    'target_id' => $meeting->getKey(),
                    'relation_type' => 'meetings.meeting',
                ]);

                $item->forceFill([
                    'linked_workhub_item_id' => $workItem->getKey(),
                    'status' => $item->status ?: 'linked',
                ])->save();

                $linked++;
            }
        });

        return ['ok' => true, 'linked' => $linked];
    }
}
