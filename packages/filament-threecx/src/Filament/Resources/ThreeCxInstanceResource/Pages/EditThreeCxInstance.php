<?php

namespace Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource;

class EditThreeCxInstance extends EditRecord
{
    protected static string $resource = ThreeCxInstanceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
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
