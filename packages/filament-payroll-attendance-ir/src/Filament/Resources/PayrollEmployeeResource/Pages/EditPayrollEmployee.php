<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource;

class EditPayrollEmployee extends EditRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = PayrollEmployeeResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->getRecord());
    }
}
