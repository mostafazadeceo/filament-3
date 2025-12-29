<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\CommentDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public CommentDto $comment, public array $meta = [])
    {
    }

    public function eventName(): string
    {
        return 'comment.created';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'comment' => $this->comment->toArray(),
            'meta' => $this->meta,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->comment->tenantId;
    }
}
