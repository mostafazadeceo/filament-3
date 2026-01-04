<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource;

class ListOvertimeRequests extends ListRecordsWithCreate
{
    protected static string $resource = OvertimeRequestResource::class;
}
