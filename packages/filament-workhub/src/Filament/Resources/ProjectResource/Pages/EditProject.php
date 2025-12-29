<?php

namespace Haida\FilamentWorkhub\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
