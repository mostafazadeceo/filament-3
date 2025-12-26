<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages;

use Filamat\IamSuite\Filament\Resources\OrganizationResource;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;

class ListOrganizations extends ListRecordsWithCreate
{
    protected static string $resource = OrganizationResource::class;
}
