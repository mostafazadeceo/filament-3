<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource;

class EditEsimGoConnection extends EditRecord
{
    protected static string $resource = EsimGoConnectionResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
