<?php

namespace Haida\FilamentWorkhub\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = auth()->id();
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        return $data;
    }
}
