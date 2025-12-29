<?php

namespace Haida\FilamentWorkhub\Observers;

use Haida\FilamentWorkhub\Models\Decision;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;

class DecisionObserver
{
    public function created(Decision $decision): void
    {
        app(WorkhubAuditService::class)->log('decision.created', null, $decision->workItem, [
            'title' => $decision->title,
        ]);
    }
}
