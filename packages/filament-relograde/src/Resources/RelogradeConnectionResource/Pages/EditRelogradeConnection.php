<?php

namespace Haida\FilamentRelograde\Resources\RelogradeConnectionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource;

class EditRelogradeConnection extends EditRecord
{
    protected static string $resource = RelogradeConnectionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['webhook_allowed_ips'])) {
            $data['webhook_allowed_ips'] = config('relograde.webhooks.allowed_ips', ['18.195.134.217']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        if (! $record instanceof RelogradeConnection) {
            return;
        }

        if ($record->is_default) {
            RelogradeConnection::query()
                ->where('environment', $record->environment)
                ->whereKeyNot($record->getKey())
                ->update(['is_default' => false]);
        }
    }
}
