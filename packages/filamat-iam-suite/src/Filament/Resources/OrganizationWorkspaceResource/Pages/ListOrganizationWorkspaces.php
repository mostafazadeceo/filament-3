<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource\Pages;

use Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;

class ListOrganizationWorkspaces extends ListRecordsWithCreate
{
    protected static string $resource = OrganizationWorkspaceResource::class;
}
