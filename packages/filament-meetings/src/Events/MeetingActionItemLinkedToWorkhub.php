<?php

namespace Haida\FilamentMeetings\Events;

use Haida\FilamentMeetings\Contracts\MeetingsEvent;
use Haida\FilamentMeetings\DTOs\MeetingActionItemDto;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingActionItemLinkedToWorkhub implements MeetingsEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public MeetingDto $meeting,
        public MeetingActionItemDto $item,
    ) {}

    public function eventName(): string
    {
        return 'meetings.action_item.linked_to_workhub';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'meeting' => $this->meeting->toArray(),
            'action_item' => $this->item->toArray(),
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->meeting->tenantId;
    }
}
