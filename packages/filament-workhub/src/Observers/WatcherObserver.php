<?php

namespace Haida\FilamentWorkhub\Observers;

use Haida\FilamentWorkhub\Models\Watcher;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;

class WatcherObserver
{
    public function created(Watcher $watcher): void
    {
        app(WorkhubAuditService::class)->log('watcher.added', null, $watcher->workItem, [
            'user_id' => $watcher->user_id,
        ]);
    }
}
