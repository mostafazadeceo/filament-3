<?php

namespace Haida\FilamentMeetings\DTOs;

use Haida\FilamentMeetings\Models\MeetingActionItem;

final class MeetingActionItemDto
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public int $meetingId,
        public string $title,
        public ?string $status,
        public ?int $assigneeId,
        public ?string $dueDate,
        public ?int $linkedWorkhubItemId,
    ) {}

    public static function fromModel(MeetingActionItem $item): self
    {
        return new self(
            $item->getKey(),
            (int) $item->tenant_id,
            (int) $item->meeting_id,
            (string) $item->title,
            $item->status,
            $item->assignee_id,
            $item->due_date?->toDateString(),
            $item->linked_workhub_item_id,
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
            'meeting_id' => $this->meetingId,
            'title' => $this->title,
            'status' => $this->status,
            'assignee_id' => $this->assigneeId,
            'due_date' => $this->dueDate,
            'linked_workhub_item_id' => $this->linkedWorkhubItemId,
        ];
    }
}
