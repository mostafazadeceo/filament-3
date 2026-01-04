<?php

namespace Tests\Feature\Meetings;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingActionItem;
use Tests\Feature\Workhub\WorkhubTestCase;

abstract class MeetingsTestCase extends WorkhubTestCase
{
    protected function createMeeting(array $data): Meeting
    {
        return Meeting::query()->create($data);
    }

    protected function createMeetingActionItem(array $data): MeetingActionItem
    {
        return MeetingActionItem::query()->create($data);
    }
}
