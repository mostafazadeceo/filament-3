<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\RoleResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\RoleResource;

class ListRoles extends ListRecordsWithCreate
{
    protected static string $resource = RoleResource::class;
}
