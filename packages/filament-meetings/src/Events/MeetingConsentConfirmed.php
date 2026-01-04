<?php

namespace Haida\FilamentMeetings\Events;

use Haida\FilamentMeetings\Contracts\MeetingsEvent;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingConsentConfirmed implements MeetingsEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public MeetingDto $meeting,
        public ?int $actorId = null,
        public string $mode = 'manual',
    ) {}

    public function eventName(): string
    {
        return 'meetings.consent.confirmed';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'meeting' => $this->meeting->toArray(),
            'actor_id' => $this->actorId,
            'mode' => $this->mode,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->meeting->tenantId;
    }
}
