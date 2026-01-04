<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoicePaymentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoicePaymentResource;

class ListCryptoInvoicePayments extends ListRecords
{
    protected static string $resource = CryptoInvoicePaymentResource::class;
}
