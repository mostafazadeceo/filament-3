<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource;

class CreateEmployeeConsent extends CreateRecord
{
    protected static string $resource = EmployeeConsentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['is_granted'])) {
            $data['granted_by'] = $data['granted_by'] ?? auth()->id();
            $data['granted_at'] = $data['granted_at'] ?? now();
            $data['revoked_at'] = null;
        }

        return $data;
    }
}
