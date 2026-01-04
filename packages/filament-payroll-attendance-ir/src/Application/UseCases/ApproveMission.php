<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\MissionRequest;

class ApproveMission
{
    public function execute(MissionRequest $request, ?int $approvedBy = null): MissionRequest
    {
        $request->update([
            'status' => 'approved',
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);

        return $request->refresh();
    }
}
