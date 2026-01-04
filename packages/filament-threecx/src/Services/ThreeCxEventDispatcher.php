<?php

namespace Haida\FilamentThreeCx\Services;

use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Haida\FilamentThreeCx\Events\ThreeCxIntegrationHealthDegraded;
use Haida\FilamentThreeCx\Events\ThreeCxMissedCallDetected;
use Haida\FilamentThreeCx\Events\ThreeCxNewContactCreatedFrom3cx;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Illuminate\Database\Eloquent\Model;

class ThreeCxEventDispatcher
{
    public function dispatchMissedCall(ThreeCxCallLog $callLog): void
    {
        event(new ThreeCxMissedCallDetected($callLog));
        $this->dispatchTrigger($callLog, ThreeCxMissedCallDetected::NAME);
    }

    public function dispatchContactCreated(ThreeCxContact $contact): void
    {
        event(new ThreeCxNewContactCreatedFrom3cx($contact));
        $this->dispatchTrigger($contact, ThreeCxNewContactCreatedFrom3cx::NAME);
    }

    public function dispatchHealthDegraded(ThreeCxInstance $instance, string $message): void
    {
        event(new ThreeCxIntegrationHealthDegraded($instance, $message));
        $this->dispatchTrigger($instance, ThreeCxIntegrationHealthDegraded::NAME);
    }

    protected function dispatchTrigger(Model $record, string $event): void
    {
        $panelId = (string) config('filament-threecx.notifications.panel', 'tenant');
        if ($panelId === '' || ! class_exists(TriggerDispatcher::class)) {
            return;
        }

        try {
            app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $record, $event);
        } catch (\Throwable) {
            // keep events resilient
        }
    }
}
