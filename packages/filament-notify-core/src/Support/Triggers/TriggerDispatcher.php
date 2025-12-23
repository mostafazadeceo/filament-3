<?php

namespace Haida\FilamentNotify\Core\Support\Triggers;

use Filament\Actions\Action;
use Haida\FilamentNotify\Core\Support\Context\ContextBuilder;
use Haida\FilamentNotify\Core\Support\Rules\RuleEngine;
use Illuminate\Database\Eloquent\Model;

class TriggerDispatcher
{
    public function __construct(
        protected TriggerService $triggerService,
        protected ContextBuilder $contextBuilder,
        protected RuleEngine $ruleEngine,
    ) {}

    public function dispatchForAction(string $panelId, Action $action): void
    {
        $trigger = $this->triggerService->upsertActionTrigger($panelId, $action);
        $context = $this->contextBuilder->buildForAction($action, $panelId);

        $this->ruleEngine->dispatch($trigger, $context);
    }

    public function dispatchForEloquent(string $panelId, Model $record, string $event): void
    {
        $trigger = $this->triggerService->upsertEloquentTrigger($panelId, $record, $event);
        $context = $this->contextBuilder->buildForEloquent($record, $event, $panelId);

        $this->ruleEngine->dispatch($trigger, $context);
    }
}
