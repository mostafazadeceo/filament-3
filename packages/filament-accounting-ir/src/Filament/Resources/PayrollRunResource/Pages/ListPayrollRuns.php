<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource;

class ListPayrollRuns extends ListRecordsWithCreate
{
    protected static string $resource = PayrollRunResource::class;
}
