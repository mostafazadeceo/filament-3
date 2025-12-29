<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource;

class ListEInvoices extends ListRecordsWithCreate
{
    protected static string $resource = EInvoiceResource::class;
}
