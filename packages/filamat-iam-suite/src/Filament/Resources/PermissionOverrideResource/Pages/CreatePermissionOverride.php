<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PermissionOverrideResource\Pages;

use Filamat\IamSuite\Filament\Resources\PermissionOverrideResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermissionOverride extends CreateRecord
{
    protected static string $resource = PermissionOverrideResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
