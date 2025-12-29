<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\PurchaseInvoiceResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\PurchaseInvoiceResource;

class ListPurchaseInvoices extends ListRecordsWithCreate
{
    protected static string $resource = PurchaseInvoiceResource::class;
}
