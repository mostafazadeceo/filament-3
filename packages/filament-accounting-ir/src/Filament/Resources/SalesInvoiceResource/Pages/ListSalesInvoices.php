<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\SalesInvoiceResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\SalesInvoiceResource;

class ListSalesInvoices extends ListRecordsWithCreate
{
    protected static string $resource = SalesInvoiceResource::class;
}
