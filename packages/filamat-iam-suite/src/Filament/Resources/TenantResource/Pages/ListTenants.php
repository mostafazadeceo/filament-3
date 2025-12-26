<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\TenantResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\TenantResource;

class ListTenants extends ListRecordsWithCreate
{
    protected static string $resource = TenantResource::class;
}
