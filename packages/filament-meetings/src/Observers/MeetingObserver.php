<?php

namespace Haida\FilamentMeetings\Observers;

use Haida\FilamentMeetings\DTOs\MeetingDto;
use Haida\FilamentMeetings\Events\MeetingCompleted;
use Haida\FilamentMeetings\Events\MeetingCreated;
use Haida\FilamentMeetings\Events\MeetingUpdated;
use Haida\FilamentMeetings\Models\Meeting;

class MeetingObserver
{
    public function created(Meeting $meeting): void
    {
        event(new MeetingCreated(MeetingDto::fromModel($meeting)));

        if ($meeting->status === 'completed') {
            event(new MeetingCompleted(MeetingDto::fromModel($meeting)));
        }
    }

    public function updated(Meeting $meeting): void
    {
        event(new MeetingUpdated(MeetingDto::fromModel($meeting)));

        if ($meeting->wasChanged('status') && $meeting->status === 'completed') {
            event(new MeetingCompleted(MeetingDto::fromModel($meeting)));
        }
    }
}
