<?php

namespace Haida\FilamentMeetings\Events;

use Haida\FilamentMeetings\Contracts\MeetingsEvent;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingAiMinutesGenerated implements MeetingsEvent
{
    /**
     * @param  array<string, mixed>  $minutes
     */
    public function __construct(
        public MeetingDto $meeting,
        public array $minutes,
    ) {}

    use Dispatchable;
    use SerializesModels;

    public function eventName(): string
    {
        return 'meetings.ai.minutes.generated';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'meeting' => $this->meeting->toArray(),
            'minutes' => $this->minutes,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->meeting->tenantId;
    }
}
