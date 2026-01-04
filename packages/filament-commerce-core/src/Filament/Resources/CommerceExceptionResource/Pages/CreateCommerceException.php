<?php

namespace Haida\FilamentCommerceCore\Filament\Resources\CommerceExceptionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceExceptionResource;

class CreateCommerceException extends CreateRecord
{
    protected static string $resource = CommerceExceptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();

        return $data;
    }
}
