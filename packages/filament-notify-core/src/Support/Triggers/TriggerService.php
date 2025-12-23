<?php

namespace Haida\FilamentNotify\Core\Support\Triggers;

use Filament\Actions\Action;
use Haida\FilamentNotify\Core\Models\Trigger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TriggerService
{
    public function upsertActionTrigger(string $panelId, Action $action): Trigger
    {
        $livewire = $action->getLivewire();
        $livewireClass = $livewire ? $livewire::class : 'unknown';
        $actionName = $action->getName();

        $key = sprintf('filament.action.%s.%s', $livewireClass, $actionName);
        $label = sprintf('%s در %s', $action->getLabel() ?? $actionName, class_basename($livewireClass));

        $record = $action->getRecord();
        $meta = [
            'livewire' => $livewireClass,
            'action' => $actionName,
            'label' => $action->getLabel(),
            'record_type' => $record instanceof Model ? $record::class : null,
            'record_id' => $record instanceof Model ? $record->getKey() : null,
            'context' => $action->getContext(),
        ];

        return Trigger::updateOrCreate(
            [
                'panel_id' => $panelId,
                'key' => $key,
            ],
            [
                'label' => $label,
                'type' => 'filament_action',
                'meta' => $meta,
            ],
        );
    }

    public function upsertEloquentTrigger(string $panelId, Model $record, string $event): Trigger
    {
        $key = sprintf('eloquent.%s.%s', $record::class, $event);
        $label = sprintf('%s: %s', class_basename($record::class), Str::headline($event));

        $meta = [
            'model' => $record::class,
            'event' => $event,
        ];

        return Trigger::updateOrCreate(
            [
                'panel_id' => $panelId,
                'key' => $key,
            ],
            [
                'label' => $label,
                'type' => 'eloquent_event',
                'meta' => $meta,
            ],
        );
    }
}
