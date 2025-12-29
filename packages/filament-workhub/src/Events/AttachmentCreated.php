<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\AttachmentDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttachmentCreated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public AttachmentDto $attachment, public array $meta = [])
    {
    }

    public function eventName(): string
    {
        return 'attachment.created';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'attachment' => $this->attachment->toArray(),
            'meta' => $this->meta,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->attachment->tenantId;
    }
}
