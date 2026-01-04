<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Filament\Resources\CryptoAuditLogResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoAuditLogResource;

class ListCryptoAuditLogs extends ListRecords
{
    protected static string $resource = CryptoAuditLogResource::class;
}
