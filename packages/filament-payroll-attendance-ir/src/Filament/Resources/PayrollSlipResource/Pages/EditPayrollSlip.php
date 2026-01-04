<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource;

class EditPayrollSlip extends EditRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = PayrollSlipResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->getRecord());
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
