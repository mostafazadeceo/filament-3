<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\TenantResource\Pages;

use Filamat\IamSuite\Filament\Resources\TenantResource;
use Filament\Resources\Pages\EditRecord;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;
}
