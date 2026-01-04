<?php

namespace Haida\FilamentPos\Filament\Resources\PosCashMovementResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentPos\Filament\Resources\PosCashMovementResource;

class CreatePosCashMovement extends CreateRecord
{
    protected static string $resource = PosCashMovementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();

        return $data;
    }
}
