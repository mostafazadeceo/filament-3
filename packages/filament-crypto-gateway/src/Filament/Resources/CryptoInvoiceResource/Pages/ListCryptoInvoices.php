<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoiceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoiceResource;

class ListCryptoInvoices extends ListRecords
{
    protected static string $resource = CryptoInvoiceResource::class;
}
