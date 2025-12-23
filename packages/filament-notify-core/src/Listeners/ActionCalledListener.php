<?php

namespace Haida\FilamentNotify\Core\Listeners;

use Filament\Actions\Action;
use Filament\Actions\Events\ActionCalled;
use Filament\Facades\Filament;
use Haida\FilamentNotify\Core\FilamentNotifyManager;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;

class ActionCalledListener
{
    public function __construct(
        protected TriggerDispatcher $dispatcher,
        protected FilamentNotifyManager $manager,
    ) {}

    public function __invoke(mixed $event): void
    {
        if ($event instanceof ActionCalled) {
            $action = $event->getAction();
        } elseif ($event instanceof Action) {
            $action = $event;
        } else {
            return;
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        if (! $panelId || ! $this->manager->isPanelEnabled($panelId)) {
            return;
        }

        $attributes = $action->getExtraAttributes();
        if (($attributes['data-fn-ignore'] ?? null) === 'true' || ($attributes['data-fn-ignore'] ?? null) === true) {
            return;
        }

        $this->dispatcher->dispatchForAction($panelId, $action);
    }
}
