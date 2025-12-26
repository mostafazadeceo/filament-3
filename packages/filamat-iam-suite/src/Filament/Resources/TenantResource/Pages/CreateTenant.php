<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\TenantResource\Pages;

use Filamat\IamSuite\Filament\Resources\TenantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}
