<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource;

class EditPayrollLeaveRequest extends EditRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = PayrollLeaveRequestResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->getRecord());
    }
}
