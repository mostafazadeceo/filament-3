<?php

namespace Haida\FilamentRelograde\Resources\RelogradeAuditLogResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentRelograde\Resources\RelogradeAuditLogResource;

class ListRelogradeAuditLogs extends ListRecordsWithCreate
{
    protected static string $resource = RelogradeAuditLogResource::class;
}
