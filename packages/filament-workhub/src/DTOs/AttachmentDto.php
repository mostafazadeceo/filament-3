<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\Attachment;

final class AttachmentDto
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public int $workItemId,
        public ?int $userId,
        public string $filename,
    ) {}

    public static function fromModel(Attachment $attachment): self
    {
        return new self(
            $attachment->getKey(),
            (int) $attachment->tenant_id,
            (int) $attachment->work_item_id,
            $attachment->user_id,
            (string) $attachment->filename
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
            'filename' => $this->filename,
        ];
    }
}
