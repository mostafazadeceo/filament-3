<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource;

class EditCatalogProduct extends EditRecord
{
    protected static string $resource = CatalogProductResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
