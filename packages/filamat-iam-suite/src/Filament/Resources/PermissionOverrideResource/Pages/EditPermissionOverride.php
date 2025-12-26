<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PermissionOverrideResource\Pages;

use Filamat\IamSuite\Filament\Resources\PermissionOverrideResource;
use Filament\Resources\Pages\EditRecord;

class EditPermissionOverride extends EditRecord
{
    protected static string $resource = PermissionOverrideResource::class;
}
