<?php

namespace Haida\FilamentMeetings\Events;

use Haida\FilamentMeetings\Contracts\MeetingsEvent;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingCompleted implements MeetingsEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public MeetingDto $meeting) {}

    public function eventName(): string
    {
        return 'meetings.completed';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'meeting' => $this->meeting->toArray(),
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->meeting->tenantId;
    }
}
