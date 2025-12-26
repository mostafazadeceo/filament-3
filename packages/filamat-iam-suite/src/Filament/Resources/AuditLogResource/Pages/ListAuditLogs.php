<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\AuditLogResource\Pages;

use Filamat\IamSuite\Filament\Resources\AuditLogResource;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;

class ListAuditLogs extends ListRecordsWithCreate
{
    protected static string $resource = AuditLogResource::class;
}
