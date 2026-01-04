<?php

namespace Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource;

class EditCommerceProduct extends EditRecord
{
    protected static string $resource = CommerceProductResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
