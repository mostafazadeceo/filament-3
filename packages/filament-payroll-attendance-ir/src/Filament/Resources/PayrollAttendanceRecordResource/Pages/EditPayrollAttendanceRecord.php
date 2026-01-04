<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceRecordResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceRecordResource;

class EditPayrollAttendanceRecord extends EditRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = PayrollAttendanceRecordResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->getRecord());
    }
}
