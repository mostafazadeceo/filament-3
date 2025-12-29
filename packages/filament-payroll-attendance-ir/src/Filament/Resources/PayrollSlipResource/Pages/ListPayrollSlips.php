<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource;

class ListPayrollSlips extends ListRecords
{
    protected static string $resource = PayrollSlipResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
