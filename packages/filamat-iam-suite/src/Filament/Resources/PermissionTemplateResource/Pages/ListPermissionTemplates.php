<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PermissionTemplateResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\PermissionTemplateResource;

class ListPermissionTemplates extends ListRecordsWithCreate
{
    protected static string $resource = PermissionTemplateResource::class;
}
