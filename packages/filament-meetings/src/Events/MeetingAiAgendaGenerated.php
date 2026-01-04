<?php

namespace Haida\FilamentMeetings\Events;

use Haida\FilamentMeetings\Contracts\MeetingsEvent;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingAiAgendaGenerated implements MeetingsEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<int, array<string, mixed>>  $agenda
     */
    public function __construct(
        public MeetingDto $meeting,
        public array $agenda,
    ) {}

    public function eventName(): string
    {
        return 'meetings.ai.agenda.generated';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'meeting' => $this->meeting->toArray(),
            'agenda' => $this->agenda,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->meeting->tenantId;
    }
}
