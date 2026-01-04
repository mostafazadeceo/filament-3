<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource;

class ListPrivilegeEligibilities extends ListRecordsWithCreate
{
    protected static string $resource = PrivilegeEligibilityResource::class;
}
