<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\LogsSensitiveAccess;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource;

class EditEmployeeConsent extends EditRecord
{
    use LogsSensitiveAccess;

    protected static string $resource = EmployeeConsentResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->logSensitiveRecord($this->record);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['is_granted'])) {
            $data['granted_by'] = $data['granted_by'] ?? auth()->id();
            $data['granted_at'] = $data['granted_at'] ?? now();
            $data['revoked_at'] = null;
        } else {
            $data['revoked_at'] = $data['revoked_at'] ?? now();
        }

        return $data;
    }
}
