<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PermissionOverrideResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\PermissionOverrideResource;

class ListPermissionOverrides extends ListRecordsWithCreate
{
    protected static string $resource = PermissionOverrideResource::class;
}
