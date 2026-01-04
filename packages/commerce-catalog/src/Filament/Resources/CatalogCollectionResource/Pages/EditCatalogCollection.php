<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource;

class EditCatalogCollection extends EditRecord
{
    protected static string $resource = CatalogCollectionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
