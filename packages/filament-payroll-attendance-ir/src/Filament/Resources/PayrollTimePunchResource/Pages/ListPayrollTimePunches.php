<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource;

class ListPayrollTimePunches extends ListRecordsWithCreate
{
    protected static string $resource = PayrollTimePunchResource::class;
}
