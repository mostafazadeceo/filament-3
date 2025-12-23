<?php

namespace Haida\FilamentNotify\Core\Support\Context;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContextBuilder
{
    public function buildForAction(Action $action, string $panelId): array
    {
        $livewire = $action->getLivewire();
        $record = $action->getRecord();
        if ($record instanceof Model) {
            $recordId = $record->getKey();
        } else {
            $recordId = null;
        }

        $actionContext = $action->getContext();

        return [
            'record' => $record,
            'record_id' => $recordId,
            'record_type' => $record instanceof Model ? $record::class : null,
            'user' => $this->resolveUser(),
            'tenant' => $this->resolveTenant(),
            'panel' => [
                'id' => $panelId,
                'path' => Filament::getCurrentPanel()?->getPath(),
            ],
            'action' => [
                'name' => $action->getName(),
                'label' => $action->getLabel(),
                'data' => $action->getData(),
                'context' => $actionContext,
                'livewire' => $livewire ? $livewire::class : null,
            ],
        ];
    }

    public function buildForEloquent(Model $record, string $event, string $panelId): array
    {
        return [
            'record' => $record,
            'record_id' => $record->getKey(),
            'record_type' => $record::class,
            'user' => $this->resolveUser(),
            'tenant' => $this->resolveTenant(),
            'panel' => [
                'id' => $panelId,
                'path' => Filament::getCurrentPanel()?->getPath(),
            ],
            'action' => [
                'name' => $event,
                'label' => Str::headline($event),
                'data' => [],
                'context' => [],
                'livewire' => null,
            ],
        ];
    }

    protected function resolveUser(): ?object
    {
        try {
            return Filament::auth()?->user();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveTenant(): ?object
    {
        try {
            return Filament::getTenant();
        } catch (\Throwable) {
            return null;
        }
    }
}
