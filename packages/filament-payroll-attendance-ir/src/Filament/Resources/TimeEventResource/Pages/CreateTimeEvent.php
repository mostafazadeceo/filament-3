<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendancePolicyEngine;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource;

class CreateTimeEvent extends CreateRecord
{
    protected static string $resource = TimeEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = TimeEventResource::sanitizePayload($data);

        return $this->applyVerification($data);
    }

    protected function afterCreate(): void
    {
        app(AttendancePolicyEngine::class)->evaluateTimeEvent($this->record);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyVerification(array $data): array
    {
        if (! empty($data['is_verified'])) {
            $data['verified_at'] = $data['verified_at'] ?? now();
            $data['verified_by'] = $data['verified_by'] ?? auth()->id();
        }

        return $data;
    }
}
