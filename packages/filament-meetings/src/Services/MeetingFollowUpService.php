<?php

namespace Haida\FilamentMeetings\Services;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingActionItem;
use Haida\FilamentMeetings\Models\MeetingMinute;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;

class MeetingFollowUpService
{
    public function notifyMinutes(Meeting $meeting, MeetingMinute $minute): void
    {
        if (! class_exists(TriggerDispatcher::class)) {
            return;
        }

        $panelId = (string) config('filament-meetings.notifications.panel', 'tenant');
        if ($panelId === '') {
            return;
        }

        try {
            app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $meeting, 'meetings.ai.minutes.generated');
        } catch (\Throwable) {
            // keep workflow resilient
        }
    }

    public function sendWeeklyDigest(?int $tenantId = null): int
    {
        if (! class_exists(TriggerDispatcher::class)) {
            return 0;
        }

        $panelId = (string) config('filament-meetings.notifications.panel', 'tenant');
        if ($panelId === '') {
            return 0;
        }

        $query = MeetingActionItem::query()
            ->where('status', 'open')
            ->whereNotNull('assignee_id')
            ->whereDate('due_date', '<=', now()->addWeek());

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $count = 0;
        foreach ($query->cursor() as $item) {
            try {
                app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $item, 'meetings.action_item.digest');
                $count++;
            } catch (\Throwable) {
                // ignore individual failures
            }
        }

        return $count;
    }
}
