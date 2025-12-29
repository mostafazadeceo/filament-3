<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource;

class ListPayrollEmployees extends ListRecordsWithCreate
{
    protected static string $resource = PayrollEmployeeResource::class;
}
