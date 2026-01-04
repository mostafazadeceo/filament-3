<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource;

class ListPayrollSlips extends ListRecordsWithCreate
{
    protected static string $resource = PayrollSlipResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
