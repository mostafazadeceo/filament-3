<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource;

class EditPayrollTimePunch extends EditRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = PayrollTimePunchResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->getRecord());
    }
}
