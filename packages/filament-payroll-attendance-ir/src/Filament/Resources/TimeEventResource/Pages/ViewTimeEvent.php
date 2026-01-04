<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource;

class ViewTimeEvent extends ViewRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = TimeEventResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->record);
    }
}
