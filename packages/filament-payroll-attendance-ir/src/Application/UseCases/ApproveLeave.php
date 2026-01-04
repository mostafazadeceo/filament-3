<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\RequestStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\LeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\States\RequestStateMachine;

class ApproveLeave
{
    public function execute(LeaveRequest $request, ?int $approvedBy = null): LeaveRequest
    {
        $current = $request->status instanceof RequestStatus ? $request->status : RequestStatus::from((string) $request->status);

        if (! RequestStateMachine::canTransition($current, RequestStatus::Approved)) {
            return $request;
        }

        $request->update([
            'status' => RequestStatus::Approved->value,
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);

        return $request->refresh();
    }
}
