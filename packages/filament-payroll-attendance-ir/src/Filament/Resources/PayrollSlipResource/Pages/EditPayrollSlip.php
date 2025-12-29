<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource;

class EditPayrollSlip extends EditRecord
{
    protected static string $resource = PayrollSlipResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
