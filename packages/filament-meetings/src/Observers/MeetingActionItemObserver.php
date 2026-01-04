<?php

namespace Haida\FilamentMeetings\Observers;

use Haida\FilamentMeetings\DTOs\MeetingActionItemDto;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Haida\FilamentMeetings\Events\MeetingActionItemCreated;
use Haida\FilamentMeetings\Events\MeetingActionItemLinkedToWorkhub;
use Haida\FilamentMeetings\Models\MeetingActionItem;

class MeetingActionItemObserver
{
    public function created(MeetingActionItem $item): void
    {
        $meeting = $item->meeting()->first();
        if (! $meeting) {
            return;
        }

        event(new MeetingActionItemCreated(
            MeetingDto::fromModel($meeting),
            MeetingActionItemDto::fromModel($item)
        ));
    }

    public function updated(MeetingActionItem $item): void
    {
        if (! $item->wasChanged('linked_workhub_item_id') || ! $item->linked_workhub_item_id) {
            return;
        }

        $meeting = $item->meeting()->first();
        if (! $meeting) {
            return;
        }

        event(new MeetingActionItemLinkedToWorkhub(
            MeetingDto::fromModel($meeting),
            MeetingActionItemDto::fromModel($item)
        ));
    }
}
