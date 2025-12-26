<?php

namespace Haida\FilamentRelograde\Resources\RelogradeConnectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource;

class CreateRelogradeConnection extends CreateRecord
{
    protected static string $resource = RelogradeConnectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['webhook_allowed_ips'])) {
            $data['webhook_allowed_ips'] = config('relograde.webhooks.allowed_ips', ['18.195.134.217']);
        }

        if (empty($data['base_url'])) {
            $data['base_url'] = config('relograde.base_url');
        }

        if (empty($data['api_version'])) {
            $data['api_version'] = config('relograde.api_version', '1.02');
        }

        return $data;
    }

    protected function afterCreate(): void
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
