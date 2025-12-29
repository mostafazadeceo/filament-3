<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\Comment;

final class CommentDto
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public int $workItemId,
        public ?int $userId,
        public string $body,
    ) {}

    public static function fromModel(Comment $comment): self
    {
        return new self(
            $comment->getKey(),
            (int) $comment->tenant_id,
            (int) $comment->work_item_id,
            $comment->user_id,
            (string) $comment->body
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
            'body' => $this->body,
        ];
    }
}
