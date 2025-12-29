<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveTypeResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveTypeResource;

class ListPayrollLeaveTypes extends ListRecordsWithCreate
{
    protected static string $resource = PayrollLeaveTypeResource::class;
}
