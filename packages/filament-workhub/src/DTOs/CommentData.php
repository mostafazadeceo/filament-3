<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\Comment;

class CommentData
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public int $workItemId,
        public ?int $userId,
        public bool $isInternal,
    ) {}

    public static function fromModel(Comment $comment): self
    {
        return new self(
            $comment->getKey(),
            (int) $comment->tenant_id,
            (int) $comment->work_item_id,
            $comment->user_id ? (int) $comment->user_id : null,
            (bool) $comment->is_internal,
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
            'work_item_id' => $this->workItemId,
            'user_id' => $this->userId,
            'is_internal' => $this->isInternal,
        ];
    }
}
