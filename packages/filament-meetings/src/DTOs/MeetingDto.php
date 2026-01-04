<?php

namespace Haida\FilamentMeetings\DTOs;

use Haida\FilamentMeetings\Models\Meeting;

final class MeetingDto
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public string $title,
        public ?string $status,
        public ?string $scheduledAt,
        public ?int $organizerId,
    ) {}

    public static function fromModel(Meeting $meeting): self
    {
        return new self(
            $meeting->getKey(),
            (int) $meeting->tenant_id,
            (string) $meeting->title,
            $meeting->status,
            $meeting->scheduled_at?->toIso8601String(),
            $meeting->organizer_id,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenantId,
            'title' => $this->title,
            'status' => $this->status,
            'scheduled_at' => $this->scheduledAt,
            'organizer_id' => $this->organizerId,
        ];
    }
}
