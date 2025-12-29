<?php

namespace Haida\FilamentWorkhub\Observers;

use Haida\FilamentWorkhub\Models\TimeEntry;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;

class TimeEntryObserver
{
    public function created(TimeEntry $timeEntry): void
    {
        app(WorkhubAuditService::class)->log('time_entry.created', null, $timeEntry->workItem, [
            'minutes' => $timeEntry->minutes,
        ]);
    }
}
