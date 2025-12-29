<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource;

class ListPayrollLeaveRequests extends ListRecordsWithCreate
{
    protected static string $resource = PayrollLeaveRequestResource::class;
}
