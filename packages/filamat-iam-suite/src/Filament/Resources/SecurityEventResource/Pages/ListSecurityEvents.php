<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\SecurityEventResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\SecurityEventResource;

class ListSecurityEvents extends ListRecordsWithCreate
{
    protected static string $resource = SecurityEventResource::class;
}
