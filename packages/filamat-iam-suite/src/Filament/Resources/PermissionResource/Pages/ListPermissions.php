<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PermissionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\PermissionResource;

class ListPermissions extends ListRecordsWithCreate
{
    protected static string $resource = PermissionResource::class;
}
