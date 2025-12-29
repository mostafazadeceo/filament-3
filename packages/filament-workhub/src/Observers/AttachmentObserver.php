<?php

namespace Haida\FilamentWorkhub\Observers;

use Haida\FilamentWorkhub\DTOs\AttachmentDto;
use Haida\FilamentWorkhub\Events\AttachmentCreated;
use Haida\FilamentWorkhub\Models\Attachment;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;

class AttachmentObserver
{
    public function created(Attachment $attachment): void
    {
        app(WorkhubAuditService::class)->log('attachment.created', null, $attachment->workItem, [
            'attachment_id' => $attachment->getKey(),
            'filename' => $attachment->filename,
        ]);

        event(new AttachmentCreated(AttachmentDto::fromModel($attachment)));
    }
}
