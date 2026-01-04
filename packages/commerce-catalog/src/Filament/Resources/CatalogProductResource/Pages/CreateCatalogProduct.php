<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource;

class CreateCatalogProduct extends CreateRecord
{
    protected static string $resource = CatalogProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
