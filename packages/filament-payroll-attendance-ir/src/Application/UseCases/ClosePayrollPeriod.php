<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;

class ClosePayrollPeriod
{
    public function execute(PayrollRun $run, ?int $actorId = null): PayrollRun
    {
        if ($run->status === 'locked') {
            return $run;
        }

        $run->update([
            'status' => 'locked',
            'locked_at' => now(),
            'approved_by' => $run->approved_by ?? $actorId ?? auth()->id(),
            'approved_at' => $run->approved_at ?? now(),
        ]);

        return $run->refresh();
    }
}
