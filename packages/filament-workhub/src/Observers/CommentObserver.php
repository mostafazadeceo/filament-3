<?php

namespace Haida\FilamentWorkhub\Observers;

use Haida\FilamentWorkhub\DTOs\CommentDto;
use Haida\FilamentWorkhub\Events\CommentCreated;
use Haida\FilamentWorkhub\Models\Comment;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        app(WorkhubAuditService::class)->log('comment.created', null, $comment->workItem, [
            'comment_id' => $comment->getKey(),
        ]);

        event(new CommentCreated(CommentDto::fromModel($comment)));
    }
}
