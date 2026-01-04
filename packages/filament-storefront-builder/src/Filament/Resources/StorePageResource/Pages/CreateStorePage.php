<?php

namespace Haida\FilamentStorefrontBuilder\Filament\Resources\StorePageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StorePageResource;

class CreateStorePage extends CreateRecord
{
    protected static string $resource = StorePageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
