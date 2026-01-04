<?php

namespace Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource;

class CreateThreeCxInstance extends CreateRecord
{
    protected static string $resource = ThreeCxInstanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->prepareConnectorKey($data);
    }

    protected function prepareConnectorKey(array $data): array
    {
        if (! empty($data['crm_connector_key'])) {
            $data['crm_connector_key_hash'] = hash('sha256', $data['crm_connector_key']);
        }

        return $data;
    }
}
