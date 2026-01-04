<?php

namespace Haida\FilamentPos\Filament\Resources\PosSaleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentPos\Filament\Resources\PosSaleResource;

class CreatePosSale extends CreateRecord
{
    protected static string $resource = PosSaleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();

        return $data;
    }
}
