<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimesheetResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimesheetResource;

class ViewTimesheet extends ViewRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = TimesheetResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->record);
    }
}
