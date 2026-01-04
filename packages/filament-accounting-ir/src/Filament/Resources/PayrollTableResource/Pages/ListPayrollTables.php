<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\PayrollTableResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollTableResource;

class ListPayrollTables extends ListRecordsWithCreate
{
    protected static string $resource = PayrollTableResource::class;
}
