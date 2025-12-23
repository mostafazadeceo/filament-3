<?php

namespace Haida\FilamentNotify\Core\Listeners;

use Filament\Facades\Filament;
use Haida\FilamentNotify\Core\FilamentNotifyManager;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Illuminate\Database\Eloquent\Model;

class EloquentEventListener
{
    public function __construct(
        protected TriggerDispatcher $dispatcher,
        protected FilamentNotifyManager $manager,
    ) {}

    /**
     * @param  array<int, mixed>  $data
     */
    public function __invoke(string $eventName, array $data): void
    {
        if (! str_starts_with($eventName, 'eloquent.')) {
            return;
        }

        $event = explode(':', $eventName)[0] ?? '';
        $event = str_replace('eloquent.', '', $event);

        $record = $data[0] ?? null;
        if (! $record instanceof Model) {
            return;
        }

        $panelId = Filament::getCurrentPanel()?->getId() ?? (config('filament-notify.enabled_panels')[0] ?? null);
        if (! $panelId || ! $this->manager->isPanelEnabled($panelId)) {
            return;
        }

        $this->dispatcher->dispatchForEloquent($panelId, $record, $event);
    }
}
