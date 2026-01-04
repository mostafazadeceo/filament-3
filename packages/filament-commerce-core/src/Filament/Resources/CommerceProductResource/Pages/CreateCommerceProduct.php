<?php

namespace Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource;

class CreateCommerceProduct extends CreateRecord
{
    protected static string $resource = CommerceProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
