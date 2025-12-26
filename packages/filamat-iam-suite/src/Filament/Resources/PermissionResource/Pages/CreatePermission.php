<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PermissionResource\Pages;

use Filamat\IamSuite\Filament\Resources\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
