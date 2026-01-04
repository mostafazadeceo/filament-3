<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource;

class CreateOvertimeRequest extends CreateRecord
{
    protected static string $resource = OvertimeRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by'] = $data['requested_by'] ?? auth()->id();

        return $data;
    }
}
