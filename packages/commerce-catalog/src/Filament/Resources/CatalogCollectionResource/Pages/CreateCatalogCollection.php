<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource;

class CreateCatalogCollection extends CreateRecord
{
    protected static string $resource = CatalogCollectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
