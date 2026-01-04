<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource;

class CreateEsimGoConnection extends CreateRecord
{
    protected static string $resource = EsimGoConnectionResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
