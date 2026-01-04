<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource;

class ListTimeEvents extends ListRecordsWithCreate
{
    protected static string $resource = TimeEventResource::class;
}
